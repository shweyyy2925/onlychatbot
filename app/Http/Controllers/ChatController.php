<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Chat;
use App\Services\GeminiService;
use App\Models\ChatHistory;


class ChatController extends Controller
{
    public function index()
    {
        $chats = Chat::latest()->get();
        return view('chatbot', compact('chats'));
    }
    public function ask(Request $request)
{
    $request->validate(['question' => 'required']);

    if (!$request->session()->has('pdf_text')) {
        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile(storage_path('app/public/pdfs/chatbotpdf.pdf'));
            $text = $pdf->getText();
            $request->session()->put('pdf_text', $text);
        } catch (\Exception $e) {
            return response()->json(['answer' => 'PDF file not found or unreadable.'], 500)
                ->header('Access-Control-Allow-Origin', 'https://troikatech.ai')
                ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With');
        }
    }

    $pdfText = $request->session()->get('pdf_text');
    $question = $request->input('question');
    $prompt = "PDF Content:\n" . $pdfText . "\n\nUser Question: " . $question;

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
        return response()->json(['answer' => 'API Error: ' . $e->getMessage()], 500)
            ->header('Access-Control-Allow-Origin', 'https://troikatech.ai')
            ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With');
    }

    Chat::create([
        'user_id'  => auth()->id() ?? null,
        'question' => $question,
        'answer'   => $answer,
    ]);

    return response()->json(['answer' => $answer])
        ->header('Access-Control-Allow-Origin', 'https://troikatech.ai')
        ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With');
}

}
