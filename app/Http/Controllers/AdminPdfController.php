<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;
use App\Models\PdfDocument;


class AdminPdfController extends Controller
{
    public function upload(Request $request)
{
    $request->validate(['pdf' => 'required|mimes:pdf']);

    $parser = new Parser();
    $pdf = $parser->parseFile($request->file('pdf')->getPathname());
    $text = $pdf->getText();

    PdfDocument::create([
        'title' => $request->file('pdf')->getClientOriginalName(),
        'content' => $text
    ]);

    return back()->with('success', 'PDF uploaded & parsed');
}
}

