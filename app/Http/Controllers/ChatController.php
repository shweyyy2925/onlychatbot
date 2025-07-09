<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Chat;


class ChatController extends Controller
{
    public function index()
    {
        $chats = Chat::latest()->get();
        return view('chat', compact('chats'));
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
                return response()->json(['answer' => 'PDF file not found or unreadable.'], 500);
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
            return response()->json(['answer' => 'API Error: ' . $e->getMessage()], 500);
        }

        Chat::create([
            'user_id'  => auth()->id() ?? null,
            'question' => $question,
            'answer'   => $answer,
        ]);

        return response()->json(['answer' => $answer]);
    }
    public function askPublic(Request $request)
{
    $request->validate(['question' => 'required']);

    // Load PDF content once per request (no session)
    try {
        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile(storage_path('app/public/pdfs/chatbotpdf.pdf'));
        $pdfText = $pdf->getText();
    } catch (\Exception $e) {
        return response()->json(['answer' => 'PDF error.'], 500);
    }

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

        $answer = $response['candidates'][0]['content']['parts'][0]['text'] ?? 'No answer.';
    } catch (\Exception $e) {
        return response()->json(['answer' => 'API Error.'], 500);
    }

    return response()->json(['answer' => $answer]);
}

}