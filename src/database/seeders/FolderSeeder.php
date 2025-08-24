<?php

namespace Database\Seeders;

use App\Models\Folder;
use App\Models\User;
use Illuminate\Database\Seeder;

class FolderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::inRandomOrder()->firstOrFail();

        Folder::factory()
            ->count(3)
            ->for($user)
            ->create()
            ->each(function ($rootFolder) use ($user) {

                Folder::factory()
                    ->count(2)
                    ->for($user)
                    ->create(['parent_folder_id' => $rootFolder->id])
                    ->each(function ($childFolder) use ($user) {

                        Folder::factory()
                            ->count(2)
                            ->for($user)
                            ->create(['parent_folder_id' => $childFolder->id]);
                    });
            });
    }
}
