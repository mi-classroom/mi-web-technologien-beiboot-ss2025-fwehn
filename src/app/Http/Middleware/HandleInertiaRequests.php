<?php

namespace App\Http\Middleware;

use App\Models\Folder;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        $folderId = $request->session()->get('current_folder_id');
        $currentFolder = null;


        $folders = collect();

        if ($user && $folderId && is_numeric($folderId)) {
            $currentFolder = Folder::where('id', $folderId)->where('user_id', $user->id)->first();

            if ($currentFolder) {
                $folders = collect();

                $folders->push($currentFolder);

                $siblings = Folder::where('parent_folder_id', $currentFolder->parent_folder_id)
                    ->where('user_id', $user->id)
                    ->where('id', '!=', $currentFolder->id)
                    ->get();
                $folders = $folders->merge($siblings);

                $parent = $currentFolder->parentFolder;

                while ($parent) {
                    $folders->push($parent);

                    $parentSiblings = Folder::where('parent_folder_id', $parent->parent_folder_id)
                        ->where('user_id', $user->id)
                        ->where('id', '!=', $parent->id)
                        ->get();

                    $folders = $folders->merge($parentSiblings);

                    $parent = $parent->parentFolder;
                }

                $children = $currentFolder->childFolders()->where('user_id', $user->id)->get();
                $folders = $folders->merge($children);

                $folders = $folders->unique('id');
            }
        } elseif ($user) {
            $folders = Folder::whereNull('parent_folder_id')->where('user_id', $user->id)->get();
        }

        $formatted = $folders->map(fn($f) => [
            'id' => $f->id,
            'name' => $f->name,
            'parent_folder_id' => $f->parent_folder_id,
        ])->values();

        $formatted = $formatted->sortBy('name')->values();

        $tree = $this->buildFolderTree($formatted);

        return [
            ...parent::share($request),

            'auth' => [
                'user' => $user,
            ],

            'folders' => $formatted,
            'foldersTree' => $tree,

            'currentFolder' => $currentFolder ? [
                'id' => $currentFolder->id,
                'name' => $currentFolder->name,
                'parent_folder_id' => $currentFolder->parent_folder_id,
            ] : null,
        ];
    }

    private function buildFolderTree($folders)
    {
        $byParent = [];

        foreach ($folders as $folder) {
            $byParent[$folder['parent_folder_id']][] = $folder;
        }

        $build = function ($parentId) use (&$build, &$byParent) {
            return collect($byParent[$parentId] ?? [])
                ->sortBy('name')
                ->map(function ($folder) use (&$build) {
                    return [
                        ...$folder,
                        'children' => $build($folder['id']),
                    ];
                })->values();

        };

        return $build(null);
    }
}
