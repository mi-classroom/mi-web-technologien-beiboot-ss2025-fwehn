<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImageRequest;
use App\Http\Requests\ImageSelectionRequest;
use App\Models\Image;
use App\Facades\ExifToolService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use ZipArchive;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    public function index(Request $request)
    {
        $query = Image::query()
            ->where('user_id', auth()->id())
            ->latest();

        $folderId = $request->session()->get('current_folder_id');

        if ($folderId && is_numeric($folderId)) {
            $query->where('folder_id', $folderId);
        } else {
            $query->whereNull('folder_id');
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
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image',
        ]);

        $folderId = $request->session()->get('current_folder_id');


        foreach ($request->file('images') as $file) {
            $path = $file->storeAs('originals', time() . '_' . $file->getClientOriginalName());

            $imageSize = getimagesize($file->getPathname());
            $width = $imageSize[0];
            $height = $imageSize[1];

            $iptc = ExifToolService::read(Storage::path($path));

            Image::create(array_merge(
                [
                    'name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                    'width' => $width,
                    'height' => $height,
                    'original_path' => $path,
                    'user_id' => auth()->id(),
                    'folder_id' => $folderId,
                ],
                $iptc
            ));
        }

        return redirect()->route('images.index');
    }

    public function preview(Image $image)
    {

        Gate::authorize('view', $image);

        if (empty($image->original_path)) {
            abort(404, 'Bildpfad fehlt oder ist ungültig');
        }

        $path = Storage::disk('public')->path($image->original_path);

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
            "image" => $image,
        ]);
    }

    public function editSelection(ImageSelectionRequest $request)
    {
        $validated = $request->validated();

        $images = Image::whereIn('id', $validated['images'])->get();

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
        return redirect()->route('images.edit', $image)->with('status.success', __('image.update.success'));
    }

    public function updateSelection(ImageSelectionRequest $request)
    {
        $validated = $request->validated();

        $images = Image::whereIn('id', $validated['images'])->get();

        $iptcFields = collect($validated)->except(['images', 'name_prefix'])->filter(function ($value) {
            return $value !== null;
        });

        foreach ($images as $image) {
            $updateData = $iptcFields->toArray();

            if (!empty($validated['name_prefix'])) {
                $updateData['name'] = $validated['name_prefix'] . $image->name;
            }

            $image->update($updateData);
        }

        return redirect()->route('images.index')->with('status.success', 'Bilder erfolgreich aktualisiert.');
    }


    public function export(Request $request, Image $image)
    {
        $validated = $request->validate([
            'format' => "nullable|in:avif,jpg,jpeg,png,webp",
        ]);

        $format = $validated['format'] ?? pathinfo($image->original_path, PATHINFO_EXTENSION);
        $format = strtolower($format);

        $originalPath = Storage::path($image->original_path);

        ExifToolService::write($originalPath, $image->iptc());

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

        if (!$img) {
            abort(500, 'Failed to load image.');
        }

        $tempPath = sys_get_temp_dir() . '/' . uniqid('export_', true) . '.' . $format;

        switch ($format) {
            case 'avif':
                imageavif($img, $tempPath);
                break;
            case 'jpeg':
            case 'jpg':
                imagejpeg($img, $tempPath);
                break;
            case 'png':
                imagepng($img, $tempPath);
                break;
            case 'webp':
                imagewebp($img, $tempPath);
                break;
            default:
                imagedestroy($img);
                abort(400, 'Unsupported target format.');
        }

        imagedestroy($img);

        return response()->download($tempPath, $image->name . '.' . $format)->deleteFileAfterSend(true);
    }

    public function exportSelection(ImageSelectionRequest $request)
    {
        $validated = $request->validated();
        $extra = $request->validate([
            'format' => 'required|in:avif,jpg,jpeg,png,webp',
        ]);

        $images = Image::whereIn('id', $validated['images'])->get();

        if ($images->isEmpty()) {
            return redirect()->back()->withErrors(['images' => 'Keine Bilder ausgewählt.']);
        }

        $zipFileName = 'export_' . now()->timestamp . '_' . Str::random(8) . '.zip';
        $zipFilePath = storage_path('app/temp/' . $zipFileName);

        if (!file_exists(dirname($zipFilePath))) {
            mkdir(dirname($zipFilePath), 0755, true);
        }

        $zip = new ZipArchive();

        if ($zip->open($zipFilePath, ZipArchive::CREATE) !== true) {
            abort(500, 'ZIP-Datei konnte nicht erstellt werden.');
        }

        foreach ($images as $image) {
            $originalPath = Storage::path($image->original_path);
            $extension = pathinfo($originalPath, PATHINFO_EXTENSION);
            $format = $extra['format'];;

            ExifToolService::write($originalPath, $image->iptc());

            $tempImagePath = $originalPath;

            if (strtolower($extension) !== $format) {
                $tempImagePath = sys_get_temp_dir() . '/' . uniqid('export_', true) . '.' . $format;

                switch (strtolower($extension)) {
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

                if (!$img) {
                    continue;
                }

                switch ($format) {
                    case 'avif':
                        imageavif($img, $tempImagePath);
                        break;
                    case 'jpeg':
                    case 'jpg':
                        imagejpeg($img, $tempImagePath);
                        break;
                    case 'png':
                        imagepng($img, $tempImagePath);
                        break;
                    case 'webp':
                        imagewebp($img, $tempImagePath);
                        break;
                    default:
                        imagedestroy($img);
                        continue 2;
                }

                imagedestroy($img);
            }

            $filenameInZip = $image->name . '.' . $format;
            $zip->addFile($tempImagePath, $filenameInZip);
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

        $path = $image->original_path;
        $image->delete();
        Storage::disk('public')->delete($path);

        return redirect()->route('images.index');
    }

    public function destroySelection(ImageSelectionRequest $request)
    {
        $validated = $request->validated();

        $images = Image::whereIn('id', $validated['images'])->get();

        foreach ($images as $image) {
            Gate::authorize('delete', $image);

            $path = $image->original_path;
            $image->delete();

            Storage::disk('public')->delete($path);
        }

        return redirect()->route('images.index');
    }
}
