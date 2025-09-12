<?php

namespace App\Jobs;

use App\Models\Image;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ProcessImagePreviews implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Image $image)
    {
    }

    public function handle(): void
    {
        $originalPath = Storage::disk('public')->path($this->image->original_path);

        if (!file_exists($originalPath)) {
            return;
        }

        Storage::disk('public')->makeDirectory('previews');

        $sizes = [
            'sm' => 200,
            'md' => 400,
            'lg' => 800,
            'xl' => 1200,
        ];

        foreach ($sizes as $key => $width) {
            $filename = pathinfo($this->image->original_path, PATHINFO_FILENAME);
            $variantPath = "previews/{$filename}_{$key}.avif";

            $img = imagecreatefromstring(file_get_contents($originalPath));
            $origWidth = imagesx($img);
            $origHeight = imagesy($img);

            $newHeight = intval(($width / $origWidth) * $origHeight);
            $resized = imagescale($img, $width, $newHeight);

            imageavif($resized, Storage::disk('public')->path($variantPath));
            imagedestroy($resized);

            $this->image->update(["{$key}_path" => $variantPath]);
        }
    }
}
