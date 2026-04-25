<?php

namespace App\Services;

use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class ImageService
{
    public function process($file, $name = 'image')
    {
        $slug = Str::slug($name);
        $filename = $slug . '-' . time();

        $path = public_path('uploads/');
        $thumbPath = public_path('uploads/thumbs/');

        if (!file_exists($path)) mkdir($path, 0755, true);
        if (!file_exists($thumbPath)) mkdir($thumbPath, 0755, true);

        // Immagine grande
        $img = Image::make($file)
            ->resize(1200, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })
            ->encode('jpg', 75);

        $img->save($path . $filename . '.jpg');

        // Thumbnail
        $thumb = Image::make($file)
            ->resize(400, null, function ($constraint) {
                $constraint->aspectRatio();
            })
            ->encode('jpg', 70);

        $thumb->save($thumbPath . $filename . '.jpg');

        return [
            'image' => 'uploads/' . $filename . '.jpg',
            'thumb' => 'uploads/thumbs/' . $filename . '.jpg',
        ];
    }
}