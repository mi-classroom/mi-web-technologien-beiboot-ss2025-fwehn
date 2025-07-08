<?php

namespace App\Http\Controllers;

use App\Http\Requests\FolderRequest;
use App\Models\Folder;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
        return redirect()->route('images.edit', $folder);
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
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FolderRequest $request, Folder $folder)
    {
        $validated = $request->validated();
        $folder->update($validated);
        return redirect()->route('folders.edit', $folder);
    }

    public function export(Request $request, Folder $folder)
    {
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Folder $folder)
    {
        //
    }

    public function select(Folder $folder)
    {
        session(['current_folder_id' => $folder->id]);
        return redirect()->route('images.index');
    }
}
