<?php

namespace App\Http\Controllers;

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
    public function index()
    {
        return Inertia::render('image/Index', [
            'images' => Image::latest()->where('user_id', auth()->id())->get(),
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
            'image' => 'required|image',
        ]);

        $file = $request->file('image');
        $filename = time() . '_' . $file->getClientOriginalName();

        $path = $file->storeAs('originals', $filename);

        $imageSize = getimagesize($file->getPathname());
        $width = $imageSize[0];
        $height = $imageSize[1];

        $iptc = ExifToolService::read(Storage::path($path));

        Image::create(array_merge(
            [
                'name' => $filename,
                'width' => $width,
                'height' => $height,
                'original_path' => $path,
                'user_id' => auth()->id(),
            ],
            $iptc
        ));

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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Image $image)
    {
        //
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
