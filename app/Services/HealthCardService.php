<?php

namespace App\Services;

use App\Models\Patient;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class HealthCardService
{
    /**
     * Generate QR code with encrypted patient data
     */
    public function generateQrCode(Patient $patient): string
    {
        // Prepare data to encrypt
        $data = [
            'patient_number' => $patient->patient_number,
            'name' => $patient->full_name,
            'date_of_birth' => $patient->date_of_birth->format('Y-m-d'),
            'blood_type' => $patient->blood_type,
            'barangay' => $patient->barangay?->name,
            'emergency_contact' => $patient->emergency_contact,
            'generated_at' => now()->toIso8601String(),
        ];

        // Encrypt data using AES-256
        $encryptedData = Crypt::encryptString(json_encode($data));

        // Generate QR code using endroid/qr-code v6 - instantiate Builder directly
        $builder = new Builder(
            writer: new PngWriter,
            data: $encryptedData,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 300,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
        );

        $result = $builder->build();

        return $result->getDataUri();
    }

    /**
     * Decrypt QR code data
     */
    public function decryptQrData(string $encryptedData): array
    {
        $decrypted = Crypt::decryptString($encryptedData);

        return json_decode($decrypted, true);
    }

    /**
     * Generate healthcard PDF in standard ID card size (3.375" x 2.125")
     */
    public function generateHealthCardPdf(Patient $patient): string
    {
        $qrCodeDataUri = $this->generateQrCode($patient);

        // Get the active health card
        $healthCard = $patient->healthCards()
            ->whereIn('status', ['active', 'suspended'])
            ->latest()
            ->first();

        // Log for debugging
        Log::info('Generating PDF for patient', [
            'patient_number' => $patient->patient_number,
            'full_name' => $patient->full_name,
            'has_qr_code' => ! empty($qrCodeDataUri),
            'card_number' => $healthCard?->card_number,
        ]);

        $pdf = Pdf::loadView('healthcard.template', [
            'patient' => $patient,
            'healthCard' => $healthCard,
            'qrCode' => $qrCodeDataUri,
        ]);

        // Use A4 size for better rendering
        $pdf->setPaper('A4', 'landscape');

        return $pdf->output();
    }

    /**
     * Save healthcard as PDF file
     */
    public function saveHealthCardPdf(Patient $patient): string
    {
        $pdfContent = $this->generateHealthCardPdf($patient);
        $filename = 'healthcard_'.$patient->patient_number.'.pdf';
        $path = 'healthcards/'.$filename;

        Storage::disk('public')->put($path, $pdfContent);

        return $path;
    }

    /**
     * Generate healthcard PNG image
     */
    public function generateHealthCardPng(Patient $patient): string
    {
        // For PNG, we'll use the same approach but save directly
        $qrCodeDataUri = $this->generateQrCode($patient);

        // Create a simple image with patient info and QR code
        // This requires GD library
        $width = 1012; // 3.375" at 300 DPI
        $height = 638; // 2.125" at 300 DPI

        $image = imagecreatetruecolor($width, $height);

        // Colors
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);
        $blue = imagecolorallocate($image, 41, 128, 185);

        // Fill background
        imagefill($image, 0, 0, $white);

        // Add border
        imagerectangle($image, 10, 10, $width - 10, $height - 10, $blue);

        // Add text (simplified - in production use proper font rendering)
        imagestring($image, 5, 30, 30, 'PANABO CITY HEALTH CARD', $blue);
        imagestring($image, 4, 30, 60, $patient->full_name, $black);
        imagestring($image, 3, 30, 90, 'Patient #: '.$patient->patient_number, $black);
        imagestring($image, 3, 30, 115, 'Blood Type: '.$patient->blood_type, $black);
        imagestring($image, 3, 30, 140, 'Barangay: '.$patient->barangay?->name, $black);

        // Convert QR code data URI to image and add it
        if (preg_match('/^data:image\/png;base64,(.+)$/', $qrCodeDataUri, $matches)) {
            $qrImageData = base64_decode($matches[1]);
            $qrImage = imagecreatefromstring($qrImageData);
            imagecopyresampled($image, $qrImage, $width - 310, $height - 310, 0, 0, 300, 300, imagesx($qrImage), imagesy($qrImage));
            imagedestroy($qrImage);
        }

        // Save to storage
        ob_start();
        imagepng($image);
        $imageData = ob_get_clean();
        imagedestroy($image);

        $filename = 'healthcard_'.$patient->patient_number.'.png';
        $path = 'healthcards/'.$filename;

        Storage::disk('public')->put($path, $imageData);

        return $path;
    }

    /**
     * Verify if a patient has a valid healthcard
     */
    public function hasValidHealthCard(Patient $patient): bool
    {
        // Check if patient has all required data
        return $patient->patient_number !== null
            && $patient->blood_type !== null
            && $patient->date_of_birth !== null;
        // && $patient->photo_path !== null;
    }
}
