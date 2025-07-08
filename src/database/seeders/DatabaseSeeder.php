<?php

namespace Database\Seeders;

use App\Models\Folder;
use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

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
