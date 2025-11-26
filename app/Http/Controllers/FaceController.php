<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FaceController extends Controller
{
     private $apiUrl = 'http://3.0.197.61:5001'; // replace with your EC2 IP

    // Register a person
  public function register(Request $request)
{
    $request->validate([
        'images.*' => 'required|file|mimes:jpg,jpeg,png'
    ]);

    $files = $request->file('images');
    if (!is_array($files)) $files = [$files];

    if (empty($files)) {
        return response()->json(['error' => 'No images uploadedSS'], 400);
    }

    $username = 'rij0311'; // or get from authenticated user

    $httpRequest = Http::withHeaders(['Accept' => 'application/json']);
    foreach ($files as $file) {
        $httpRequest = $httpRequest->attach(
            'images[]', file_get_contents($file->getRealPath()), $file->getClientOriginalName()
        );
    }

    $httpRequest = $httpRequest->attach('name', $username)
                               ->post($this->apiUrl . '/register');

    return response()->json($httpRequest->json());
}
    // Recognize a face
    public function recognize(Request $request)
    {
        $request->validate([
            'image' => 'required|image'
        ]);

        $file = $request->file('image');

        $response = Http::attach(
            'file', file_get_contents($file->getRealPath()), $file->getClientOriginalName()
        )->post($this->apiUrl . '/recognize');

        return response()->json($response->json());
    }

    // Delete a person
    public function delete($person_id)
    {
        $response = Http::delete($this->apiUrl . '/delete/' . $person_id);

        return response()->json($response->json());
    }
}
