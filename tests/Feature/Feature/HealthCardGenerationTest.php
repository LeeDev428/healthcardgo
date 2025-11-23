<?php

use App\Models\Barangay;
use App\Models\Patient;
use App\Models\User;
use App\Services\HealthCardService;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');

    // Seed barangays
    $this->artisan('db:seed', ['--class' => 'BarangaySeeder']);

    $this->user = User::factory()->create([
        'role_id' => 4, // Patient role
        'status' => 'active',
    ]);

    $this->patient = Patient::factory()->create([
        'user_id' => $this->user->id,
        'blood_type' => 'O+',
        'photo_path' => 'patient-photos/test.jpg',
        'barangay_id' => Barangay::first()->id,
    ]);

    $this->healthCardService = app(HealthCardService::class);
});

test('can generate QR code with encrypted patient data', function () {
    $qrCode = $this->healthCardService->generateQrCode($this->patient);
    expect($qrCode)->toBeString()
        ->and($qrCode)->toStartWith('data:image/png;base64,');
});

test('can decrypt QR code data', function () {
    // Extract encrypted data from QR code generation
    $data = [
        'patient_number' => $this->patient->patient_number,
        'name' => $this->patient->user->name,
        'date_of_birth' => $this->patient->date_of_birth->format('Y-m-d'),
        'blood_type' => $this->patient->blood_type,
        'barangay' => $this->patient->barangay?->name,
        'emergency_contact' => $this->patient->emergency_contact,
        'generated_at' => now()->toIso8601String(),
    ];

    $encrypted = \Illuminate\Support\Facades\Crypt::encryptString(json_encode($data));
    $decrypted = $this->healthCardService->decryptQrData($encrypted);

    expect($decrypted)->toBeArray()
        ->and($decrypted['patient_number'])->toBe($this->patient->patient_number)
        ->and($decrypted['blood_type'])->toBe($this->patient->blood_type);
});

test('can generate healthcard PDF', function () {
    $pdfContent = $this->healthCardService->generateHealthCardPdf($this->patient);

    expect($pdfContent)->toBeString()
        ->and(strlen($pdfContent))->toBeGreaterThan(0);
});

test('can save healthcard PDF to storage', function () {
    $path = $this->healthCardService->saveHealthCardPdf($this->patient);

    expect($path)->toBeString()
        ->and(Storage::disk('public')->exists($path))->toBeTrue();
});

test('validates patient has all required data for healthcard', function () {
    $result = $this->healthCardService->hasValidHealthCard($this->patient);

    expect($result)->toBeTrue();
});

test('validates patient missing required data cannot generate healthcard', function () {
    $incompletePatient = Patient::factory()->create([
        'user_id' => User::factory()->create(['role_id' => 4]),
        'blood_type' => null, // Missing blood type
        'photo_path' => null, // Missing photo
    ]);

    $result = $this->healthCardService->hasValidHealthCard($incompletePatient);

    expect($result)->toBeFalse();
});

test('healthcard PDF has standard ID card dimensions', function () {
    expect(fn () => $this->healthCardService->generateHealthCardPdf($this->patient))
        ->not->toThrow(\Exception::class);
});

test('healthcard template contains patient details before PDF render', function () {
    $html = view('healthcard.template', [
        'patient' => $this->patient,
        'qrCode' => $this->healthCardService->generateQrCode($this->patient),
    ])->render();

    expect($html)->toBeString()
        ->and($html)->toContain((string) $this->patient->patient_number)
        ->and($html)->toContain($this->patient->full_name);
});

test('QR code contains all required patient information', function () {
    $data = [
        'patient_number' => $this->patient->patient_number,
        'name' => $this->patient->user->name,
        'date_of_birth' => $this->patient->date_of_birth->format('Y-m-d'),
        'blood_type' => $this->patient->blood_type,
        'barangay' => $this->patient->barangay?->name,
        'emergency_contact' => $this->patient->emergency_contact,
        'generated_at' => now()->toIso8601String(),
    ];

    $encrypted = \Illuminate\Support\Facades\Crypt::encryptString(json_encode($data));
    $decrypted = $this->healthCardService->decryptQrData($encrypted);

    expect($decrypted)->toHaveKeys([
        'patient_number',
        'name',
        'date_of_birth',
        'blood_type',
        'barangay',
        'emergency_contact',
        'generated_at',
    ]);
});
