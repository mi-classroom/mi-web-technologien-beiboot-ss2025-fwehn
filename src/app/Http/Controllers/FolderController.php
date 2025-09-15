<?php

namespace App\Http\Controllers;

use App\FolderOperation;
use App\Http\Requests\FolderRequest;
use App\Models\Folder;
use App\Models\IptcDataEntry;
use App\Facades\ExifToolService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use ZipArchive;

class FolderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(FolderRequest $request)
    {
        $validated = $request->validated();
        $folder = Folder::create([...$validated, "user_id" => auth()->user()->id]);
        return redirect()->route('folders.select', $folder)->with('status.success', __('folder.store.success'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Folder $folder)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Folder $folder)
    {
        return Inertia::render('folder/Edit', [
            'folder' => $folder->load('iptc'),
            'current_folder_id' => $folder->id
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FolderRequest $request, Folder $folder)
    {
        $validated = $request->validated();
        $folder->update($validated);
        $folderIptc = $folder->iptc()->updateOrCreate([], !empty($validated['iptc']) ? $validated['iptc'] : []);

        $operation = FolderOperation::tryFrom($validated["operation"]);

        switch ($operation) {
            case FolderOperation::PROPAGATE:
                foreach ($folder->images as $image) {
                    $image->iptc()->updateOrCreate([], $folderIptc->toArray());
                }
                break;

            case FolderOperation::MERGE:
                foreach ($folder->images as $image) {
                    $updateData = array_filter(
                        $folderIptc->only((new IptcDataEntry())->getFillable()) ?? [],
                        fn($key) => !$image->iptc?->$key,
                        ARRAY_FILTER_USE_KEY
                    );

                    if (!empty($updateData)) {
                        $image->iptc()->updateOrCreate([], $updateData);
                    }
                }
                break;

            case FolderOperation::SAVE:
            default:
        }

        return redirect()->route('folders.edit', $folder)
            ->with(
                'status.success',
                trans_choice(
                    'folder.update.success',
                    ($operation === FolderOperation::SAVE || $operation === null) ? 1 : 2
                )
            );
    }

    public function export(Request $request, Folder $folder)
    {
        $extra = $request->validate([
            'format' => 'required|in:avif,jpg,jpeg,png,webp',
        ]);

        $images = $folder->images()->with('iptc')->get();

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
            $format = $extra['format'];

            ExifToolService::write($originalPath, $image->iptc?->toArray() ?? []);
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

        return response()->download($zipFilePath, $folder->name . '.zip')->deleteFileAfterSend(true);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Folder $folder)
    {
        foreach ($folder->images as $image) {
            $image->iptc()?->delete();
        }

        $folder->delete();

        return redirect()->route('folders.index')->with('status.success', __('folder.destroy.success'));
    }

    public function select(Request $request, Folder $folder = null)
    {
        if (is_null($folder)) session(['current_folder_id' => null]);
        else session(['current_folder_id' => $folder->id]);

        if ($request->route()->getName() === 'folders.select-and-edit') {
            return redirect()->route('folders.edit', $folder);
        }

        return redirect()->route('images.index');
    }
}
