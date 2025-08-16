<?php

namespace App\Http\Controllers;

use App\FolderOperation;
use App\Http\Requests\FolderRequest;
use App\Models\Folder;
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
        $folder = Folder::create($validated);
        return redirect()->route('folders.edit', $folder)->with('status.success', __('folder.store.success'));
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
            'folder' => $folder,
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

        $operation = FolderOperation::tryFrom($validated["operation"]);
        switch ($operation) {
            case FolderOperation::PROPAGATE:
                $folder->images()->update(array_filter(
                    $validated,
                    fn($key) => Str::startsWith($key, 'iptc_'),
                    ARRAY_FILTER_USE_KEY
                ));
                break;
            case FolderOperation::MERGE:
                foreach ($folder->images as $image) {
                    $updateData = array_filter(
                        $validated,
                        fn($key) => Str::startsWith($key, 'iptc_') && $image->$key === null,
                        ARRAY_FILTER_USE_KEY
                    );

                    if (!empty($updateData)) {
                        $image->update($updateData);
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

        $images = $folder->images()->get();

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

        return response()->download($zipFilePath, $folder->name . '.zip')->deleteFileAfterSend(true);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Folder $folder)
    {
        //
    }

    public function select(Request $request, Folder $folder)
    {
        session(['current_folder_id' => $folder->id]);

        if ($request->route()->getName() === 'folders.select-and-edit') {
            return redirect()->route('folders.edit', $folder);
        }

        return redirect()->route('images.index');
    }
}
