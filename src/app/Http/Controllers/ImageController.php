<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImageRequest;
use Intervention\Image\Facades\Image as InterventionImage;
use App\Models\Image;
use App\Services\ExifToolService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $images = Image::latest()->where('user_id', auth()->id())->get();

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

    public function editSelection()
    {
        return Inertia::render('image/EditSelection', [
            "image" => Image::inRandomOrder()->first(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ImageRequest $request, Image $image)
    {
        $validated = $request->validated();

        $image->update($validated);
        return redirect()->route('images.edit', $image);
    }

    public function export(Request $request, Image $image)
    {
        $validated = $request->validate([
            'format' => "nullable|in:jpg,jpeg,png,webp",
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
            case 'jpeg':
            case 'jpg':
                imagejpeg($img, $tempPath);
                break;
            case 'png':
                imagepng($img, $tempPath);
                break;
            default:
                imagedestroy($img);
                abort(400, 'Unsupported target format.');
        }

        imagedestroy($img);

        return response()->download($tempPath, $image->name . '.' . $format)->deleteFileAfterSend(true);
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
}
