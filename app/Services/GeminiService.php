<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeminiService
{
    public static function ask($question, $context)
    {
        $prompt = "Context:\n" . $context . "\n\nUser question: " . $question;

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . env('GEMINI_API_KEY'), [
            'contents' => [
                ['parts' => [['text' => $prompt]]]
            ]
        ]);

        return $response['candidates'][0]['content']['parts'][0]['text'] ?? 'No answer.';
    }
}
