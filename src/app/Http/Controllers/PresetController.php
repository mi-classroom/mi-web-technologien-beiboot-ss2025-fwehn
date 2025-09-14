<?php

namespace App\Http\Controllers;

use App\Http\Requests\PresetRequest;
use App\Models\Preset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function Pest\Laravel\json;

class PresetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function list()
    {
        return response()->json(Auth::user()->presets()->with(['iptc'])->get());
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
    public function store(PresetRequest $request)
    {
        $validated = $request->validated();
        $preset = Preset::create([...$validated, 'user_id' => auth()->user()->id]);
        $preset->iptc()->create($validated["iptc"]);
        return redirect()->back()->with('status.success', __('preset.store.success'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Preset $preset)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Preset $preset)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Preset $preset)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Preset $preset)
    {
        $preset->iptc()->delete();
        $preset->delete();
        return redirect()->back()->with('status.success', __('preset.destroy.success'));
    }
}
