<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PdfParserService;

class PdfUploadController extends Controller
{
    protected $pdfParser;

    public function __construct(PdfParserService $pdfParser)
    {
        $this->pdfParser = $pdfParser;
    }

    public function upload(Request $request)
    {
        
        $request->validate(['file' => 'required|mimes:pdf|max:2048']);
       
        try {
            $pdfPath = $request->file('file')->getRealPath();
            $parsedData = $this->pdfParser->parse($pdfPath);
            return response()->json($parsedData, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

