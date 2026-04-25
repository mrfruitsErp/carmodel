<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ImageService;

class ImageController extends Controller
{
    public function upload(Request $request, ImageService $imageService)
    {
        $request->validate([
            'image' => 'required|image|max:5000'
        ]);

        $file = $request->file('image');

        $result = $imageService->process($file, 'auto');

        return response()->json($result);
    }
}