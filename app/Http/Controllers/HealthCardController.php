<?php

namespace App\Http\Controllers;

use App\Models\HealthCard;
use Illuminate\Support\Facades\Storage;

class HealthCardController extends Controller
{
    public function downloadPdf(HealthCard $healthCard)
    {
        $pdfPath = $healthCard->medical_data['pdf_path'] ?? null;

        if (! $pdfPath || ! Storage::disk('public')->exists($pdfPath)) {
            return back()->with('error', 'PDF file not found. Please regenerate the health card.');
        }

        return response()->download(
            Storage::disk('public')->path($pdfPath),
            'healthcard_'.$healthCard->card_number.'.pdf'
        );
    }

    public function downloadPng(HealthCard $healthCard)
    {
        $pngPath = $healthCard->medical_data['png_path'] ?? null;

        if (! $pngPath || ! Storage::disk('public')->exists($pngPath)) {
            return back()->with('error', 'PNG file not found. Please regenerate the health card.');
        }

        return response()->download(
            Storage::disk('public')->path($pngPath),
            'healthcard_'.$healthCard->card_number.'.png'
        );
    }
}
