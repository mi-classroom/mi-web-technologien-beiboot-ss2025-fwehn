<?php

namespace Database\Seeders;

use App\Facades\ExifToolService;
use App\Models\Folder;
use App\Models\Image;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
//        $folder = Folder::first();
        $user = User::first();

        $seedImagesPath = database_path('seeders/images');

        $files = glob($seedImagesPath . '/*.{jpg,jpeg,png}', GLOB_BRACE);

        foreach ($files as $filePath) {
            $uploadedFile = new UploadedFile(
                $filePath,
                basename($filePath),
                mime_content_type($filePath),
                null,
                true
            );

            $fileName = Str::uuid();
            $path = $uploadedFile->storeAs(
                'originals',
                "$fileName.{$uploadedFile->extension()}"
            );

            $imageSize = getimagesize($uploadedFile->getPathname());
            $width = $imageSize[0];
            $height = $imageSize[1];

            $iptc = ExifToolService::read(Storage::path($path));

            $image = Image::create([
                'name' => $fileName,
                'width' => $width,
                'height' => $height,
                'original_path' => $path,
                'user_id' => $user->id,
                'folder_id' => null,
            ]);

            if (!empty($iptc)) {
                $image->iptc()->create($iptc);
            }
        }
    }
}
