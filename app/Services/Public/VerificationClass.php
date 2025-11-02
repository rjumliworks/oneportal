<?php

namespace App\Services\Public;

use Hashids\Hashids;
use Illuminate\Support\Facades\Storage;

class VerificationClass
{
    public function verify(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:pdf|max:10240'
    ]);

    $file = $request->file('file');
    $content = file_get_contents($file->getRealPath());

    // Extract the embedded HMAC
    preg_match('/\/TamperHash\s*\((.*?)\)/', $content, $matches);
    $embedded = $matches[1] ?? null;

    if (!$embedded) {
        return inertia('Verification/Result', [
            'status' => 'error',
            'message' => 'No digital key found in the file.'
        ]);
    }

    // Recompute the HMAC for the uploaded file (excluding metadata)
    $clean = preg_replace('/\/TamperHash\s*\(.*?\)/', '', $content);
    $recomputed = hash_hmac('sha256', $clean, config('app.key'));

    $isValid = hash_equals($embedded, $recomputed);
dd($isValid);
    return inertia('Verification/Result', [
        'status' => $isValid ? 'valid' : 'tampered',
        'embedded' => $embedded,
        'recomputed' => $recomputed
    ]);
}
}
