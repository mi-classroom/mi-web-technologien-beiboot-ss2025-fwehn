<?php

namespace App\Http\Controllers;

use App\FolderOperation;
use App\Http\Requests\ImageRequest;
use App\Http\Requests\ImageSelectionRequest;
use App\Models\Folder;
use App\Models\Image;
use App\Models\IptcDataEntry;
use App\Facades\ExifToolService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use ZipArchive;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    public function index(Request $request)
    {
        $query = Image::query()
            ->with(['iptc'])
            ->where('user_id', auth()->id())
            ->latest();

        $folderId = $request->session()->get('current_folder_id');

        if ($folderId && is_numeric($folderId)) {
            $query->where('folder_id', $folderId);
        } else {
            $query->whereNull('folder_id');
        }

        $filterableFields = (new IptcDataEntry())->getFillable();

        foreach ($request->all() as $field => $value) {
            if (in_array($field, $filterableFields, true)) {
                $query->whereHas('iptc', function ($q) use ($field, $value) {
                    if ($value === 'true' || $value === true) {
                        $q->whereNotNull($field);
                    } elseif ($value === 'false' || $value === false) {
                        $q->whereNull($field);
                    }
                });
            }
        }

        $images = $query->get();

        return Inertia::render('image/Index', [
            'images' => $images,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'image' => 'required|image',
            'operation' => ['required', Rule::enum(FolderOperation::class)]
        ]);

        $folderId = $request->session()->get('current_folder_id');

        $file = $request->file('image');
        $path = $file->storeAs('originals', time() . '_' . $file->getClientOriginalName());

        $imageSize = getimagesize($file->getPathname());
        $width = $imageSize[0];
        $height = $imageSize[1];

        $iptc = ExifToolService::read(Storage::path($path));

        $image = Image::create([
            'name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'width' => $width,
            'height' => $height,
            'original_path' => $path,
            'user_id' => auth()->id(),
            'folder_id' => $folderId,
        ]);

        if (!empty($iptc)) {
            $image->iptc()->create($iptc);
        }

        $folder = Folder::with('iptc')->find($folderId);
        $operation = FolderOperation::tryFrom($validated["operation"]);

        if ($folder && !in_array($operation, [FolderOperation::SAVE, null])) {
            $folderIPTC = $folder->iptc?->only($image->iptc()->getModel()->getFillable()) ?? [];

            match ($operation) {
                FolderOperation::PROPAGATE => $image->iptc()->update($folderIPTC),
                FolderOperation::MERGE => $image->iptc()->update(array_filter(
                    $folderIPTC,
                    fn($key) => $image->iptc->$key === null,
                    ARRAY_FILTER_USE_KEY
                )),
                default => null
            };
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'image' => $image->load('iptc')
            ]);
        }

        return redirect()->route('images.index')
            ->with('status.success', __('image.store.success', ["imageName" => $image->name]));
    }

    public function preview(Request $request, Image $image)
    {
        Gate::authorize('view', $image);

        $w = (int)$request->query('w');
        $supportsAvif = str_contains($request->header('Accept', ''), 'image/avif');

        $map = [
            200 => 'sm_path',
            400 => 'md_path',
            800 => 'lg_path',
            1200 => 'xl_path',
        ];

        $path = null;

        if ($supportsAvif && isset($map[$w])) {
            $field = $map[$w];
            $path = $image->$field ? Storage::disk('public')->path($image->$field) : null;
        }

        if (!$path || !file_exists($path)) {
            $path = Storage::disk('public')->path($image->original_path);
        }

        if (!file_exists($path)) {
            abort(404, 'Bild nicht gefunden');
        }

        return response()->file($path);
    }


    /**
     * Display the specified resource.
     */
    public function show(Image $image)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Image $image)
    {
        return Inertia::render('image/Edit', [
            "image" => $image->load('iptc'),
        ]);
    }

    public function editSelection(ImageSelectionRequest $request)
    {
        $validated = $request->validated();

        $images = Image::with('iptc')->whereIn('id', $validated['images'])->get();

        return Inertia::render('image/EditSelection', [
            "images" => $images,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ImageRequest $request, Image $image)
    {
        $validated = $request->validated();

        $image->update($validated);

        if (!empty($validated["iptc"])) {
            $image->iptc()->updateOrCreate([], $validated["iptc"]);
        }

        return redirect()->route('images.edit', $image)->with('status.success', __('image.update.success'));
    }

    public function updateSelection(ImageSelectionRequest $request)
    {
//        TODO fortlaufende bildbenennung

        $validated = $request->validated();

        $images = Image::with('iptc')->whereIn('id', $validated['images'])->get();

        foreach ($images as $image) {
            if (!empty($validated['iptc'])) {
                $image->iptc()->updateOrCreate([], $validated['iptc']);
            }
        }

        return redirect()->route('images.index')->with('status.success', 'Bilder erfolgreich aktualisiert.');
    }

    public function export(Request $request, Image $image)
    {
        $validated = $request->validate([
            'format' => "nullable|in:avif,jpg,jpeg,png,webp",
        ]);

        $format = strtolower($validated['format'] ?? pathinfo($image->original_path, PATHINFO_EXTENSION));
        $originalPath = Storage::path($image->original_path);

        ExifToolService::write($originalPath, $image->iptc?->toArray() ?? []);

        if ($format === strtolower(pathinfo($originalPath, PATHINFO_EXTENSION))) {
            return Storage::disk('public')->download(
                $image->original_path,
                $image->name . '.' . $format
            );
        }

        $extension = strtolower(pathinfo($originalPath, PATHINFO_EXTENSION));
        switch ($extension) {
            case 'jpeg':
            case 'jpg':
                $img = imagecreatefromjpeg($originalPath);
                break;
            case 'png':
                $img = imagecreatefrompng($originalPath);
                break;
            default:
                abort(400, 'Unsupported image format.');
        }

        if (!$img) abort(500, 'Failed to load image.');

        $tempPath = sys_get_temp_dir() . '/' . uniqid('export_', true) . '.' . $format;

        match ($format) {
            'avif' => imageavif($img, $tempPath),
            'jpeg', 'jpg' => imagejpeg($img, $tempPath),
            'png' => imagepng($img, $tempPath),
            'webp' => imagewebp($img, $tempPath),
            default => abort(400, 'Unsupported target format.'),
        };

        imagedestroy($img);

        return response()->download($tempPath, $image->name . '.' . $format)->deleteFileAfterSend(true);
    }

    public function exportSelection(ImageSelectionRequest $request)
    {
        $validated = $request->validated();
        $extra = $request->validate([
            'format' => 'required|in:avif,jpg,jpeg,png,webp',
        ]);

        $images = Image::with('iptc')->whereIn('id', $validated['images'])->get();

        if ($images->isEmpty()) {
            return redirect()->back()->withErrors(['images' => 'Keine Bilder ausgewählt.']);
        }

        $zipFileName = 'export_' . now()->timestamp . '_' . Str::random(8) . '.zip';
        $zipFilePath = storage_path('app/temp/' . $zipFileName);

        if (!file_exists(dirname($zipFilePath))) mkdir(dirname($zipFilePath), 0755, true);

        $zip = new ZipArchive();
        if ($zip->open($zipFilePath, ZipArchive::CREATE) !== true) abort(500, 'ZIP-Datei konnte nicht erstellt werden.');

        foreach ($images as $image) {
            $originalPath = Storage::path($image->original_path);
            $format = $extra['format'];

            ExifToolService::write($originalPath, $image->iptc?->toArray() ?? []);
            $tempImagePath = $originalPath;

            if (strtolower(pathinfo($originalPath, PATHINFO_EXTENSION)) !== $format) {
                switch (strtolower(pathinfo($originalPath, PATHINFO_EXTENSION))) {
                    case 'jpeg':
                    case 'jpg':
                        $img = imagecreatefromjpeg($originalPath);
                        break;
                    case 'png':
                        $img = imagecreatefrompng($originalPath);
                        break;
                    default:
                        continue 2;
                }

                if (!$img) continue;

                match ($format) {
                    'avif' => imageavif($img, $tempImagePath),
                    'jpeg', 'jpg' => imagejpeg($img, $tempImagePath),
                    'png' => imagepng($img, $tempImagePath),
                    'webp' => imagewebp($img, $tempImagePath),
                    default => imagedestroy($img)
                };

                imagedestroy($img);
            }

            $zip->addFile($tempImagePath, $image->name . '.' . $format);
        }

        $zip->close();

        return response()->download($zipFilePath, 'images_export.zip')->deleteFileAfterSend(true);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Image $image)
    {
        Gate::authorize('delete', $image);

        $image->iptc()?->delete();
        $path = $image->original_path;
        $image->delete();
        Storage::disk('public')->delete($path);

        return redirect()->route('images.index')->with('status.success', __('image.destroy.success'));
    }

    public function destroySelection(ImageSelectionRequest $request)
    {
        $validated = $request->validated();

        $images = Image::with('iptc')->whereIn('id', $validated['images'])->get();

        foreach ($images as $image) {
            Gate::authorize('delete', $image);
            $image->iptc()?->delete();
            Storage::disk('public')->delete($image->original_path);
            $image->delete();
        }

        return redirect()->route('images.index')->with('status.success', __('image.destroySelection.success'));
    }
}
