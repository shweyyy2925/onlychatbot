<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Chat;
use App\Services\GeminiService;
use App\Models\ChatHistory;


class ChatController extends Controller
{
    public function ask(Request $request)
{
    $request->validate(['question' => 'required']);

    if (!$request->session()->has('pdf_text')) {
        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile(storage_path('app/public/pdfs/chatbotpdf.pdf'));
        $text = $pdf->getText();
        $request->session()->put('pdf_text', $text);
    }

    $pdfText = $request->session()->get('pdf_text');
    $question = $request->input('question');

    $prompt = "PDF Content:\n" . $pdfText . "\n\nUser Question: " . $question;

    // ✅ This is your Gemini call wrapped in error handling
    try {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . env('GEMINI_API_KEY'), [
            'contents' => [
                ['parts' => [['text' => $prompt]]]
            ]
        ]);

        $answer = $response['candidates'][0]['content']['parts'][0]['text'] ?? 'No answer';

    } catch (\Exception $e) {
        return response()->json([
            'answer' => 'API Error: ' . $e->getMessage()
        ], 500);
    }

    // Optional: Save to DB
    Chat::create([
        'user_id'  => auth()->id() ?? null,
        'question' => $question,
        'answer'   => $answer,
    ]);

    // Return JSON for frontend
    return response()->json(['answer' => $answer]);
}


    public function index()
    {
        $chats = Chat::latest()->get();
        return view('chatbot', compact('chats'));
    }
}
