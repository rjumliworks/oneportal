<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\RekognitionService;

class RekognitionController extends Controller
{
    public function detect(Request $request, RekognitionService $rekognition)
    {
        $request->validate([
            'image' => 'required|image|max:5120',
        ]);

        $path = $request->file('image')->getPathName();

        $result = $rekognition->detectLabels($path); // or detectText()

        return response()->json($result);
    }
}
