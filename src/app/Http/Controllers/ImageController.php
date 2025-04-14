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
        return Inertia::render('image/Show', [
            "image" => $image,
        ]);
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
        Gate::authorize('update', $image);

        $validated = $request->validate([
            'name' => 'required|string',

            'iptc_object_attribute_reference' => 'nullable|string',
            'iptc_object_name' => 'nullable|string',
            'iptc_subject_reference' => 'nullable|string',
            'iptc_special_instructions' => 'nullable|string',
            'iptc_date_created' => 'nullable', // TODO Date
            'iptc_time_created' => 'nullable', // TODO Time
            'iptc_byline' => 'nullable|string',
            'iptc_byline_title' => 'nullable|string',
            'iptc_city' => 'nullable|string',
            'iptc_sub_location' => 'nullable|string',
            'iptc_province_state' => 'nullable|string',
            'iptc_country_primary_location_code' => 'nullable|string',
            'iptc_country_primary_location_name' => 'nullable|string',
            'iptc_original_transmission_reference' => 'nullable|string',
            'iptc_headline' => 'nullable|string',
            'iptc_credit' => 'nullable|string',
            'iptc_source' => 'nullable|string',
            'iptc_copyright_notice' => 'nullable|string',
            'iptc_caption_abstract' => 'nullable|string',
            'iptc_writer_editor' => 'nullable|string',
            'iptc_application_record_version' => 'nullable|integer'
        ]);

        $image->update($validated);
        return redirect()->route('images.show', $image);
    }

    public function export(Image $image)
    {
        ExifToolService::write(Storage::path($image->original_path), $image->iptc());

        return Storage::disk('public')->download(
            $image->original_path,
            $image->name . '.' . pathinfo($image->original_path, PATHINFO_EXTENSION)
        );
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
