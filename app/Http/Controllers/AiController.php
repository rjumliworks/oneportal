<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class AiController extends Controller
{
    public function improve(Request $request)
    {
        $request->validate([
            'purpose' => 'required|string'
        ]);

        $client = new Client();

        $response = $client->post('https://api.groq.com/openai/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . config('app.groq_key'),
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => 'llama-3.3-70b-versatile',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a professional editor. Improve the grammar and clarity of the text by editing only the user\'s words. Do NOT add new ideas, context, explanations, summaries, or phrases such as \'purpose\', \'This request\', or anything similar. Return exactly one improved version as a single sentence.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $request->purpose,
                    ],
                ],
            ]
        ]);

        $result = json_decode($response->getBody(), true);
        return response()->json([
            'improved' => $result['choices'][0]['message']['content']
        ]);
    }
}
