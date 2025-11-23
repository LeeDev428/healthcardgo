<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Health Card - {{ $patient->patient_number }}</title>
    <style>
        body {
            margin: 0;
            padding: 10px;
            font-family: Arial, Helvetica, sans-serif;
        }
        .health-card {
            max-width: 100%;
            margin: 0 auto;
            border: 3px solid #2563eb;
            background: #ffffff;
            padding: 0;
        }
        .header {
            background: #2563eb;
            color: white;
            padding: 12px;
            text-align: center;
            border-bottom: 3px solid #1e40af;
        }
        .header h1 {
            margin: 0 0 3px 0;
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .header h2 {
            margin: 0;
            font-size: 11px;
            font-weight: normal;
        }
        .card-body {
            padding: 15px;
        }
        .content-wrapper {
            display: table;
            width: 100%;
        }
        .patient-info {
            display: table-cell;
            width: 60%;
            vertical-align: top;
            padding-right: 15px;
        }
        .qr-section {
            display: table-cell;
            width: 40%;
            vertical-align: top;
            text-align: center;
        }
        .photo-container {
            margin-bottom: 10px;
        }
        .photo-container img {
            width: 70px;
            height: 70px;
            border: 2px solid #2563eb;
        }
        .info-item {
            margin-bottom: 8px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 4px;
        }
        .info-label {
            font-size: 8px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 2px;
            font-weight: bold;
        }
        .info-value {
            font-size: 12px;
            color: #111827;
            font-weight: bold;
        }
        .qr-container {
            background: #f9fafb;
            border: 2px solid #2563eb;
            padding: 10px;
        }
        .qr-container img {
            display: block;
            margin: 0 auto;
        }
        .qr-text {
            margin-top: 6px;
            font-size: 9px;
            color: #374151;
            font-weight: bold;
        }
        .footer {
            background: #f3f4f6;
            padding: 10px;
            text-align: center;
            font-size: 9px;
            color: #4b5563;
            border-top: 2px solid #e5e7eb;
        }
        .footer strong {
            color: #1f2937;
        }
        .card-number {
            background: #fef3c7;
            border: 2px solid #f59e0b;
            padding: 8px;
            text-align: center;
            margin-bottom: 12px;
        }
        .card-number-label {
            font-size: 8px;
            color: #92400e;
            font-weight: bold;
            margin-bottom: 2px;
        }
        .card-number-value {
            font-size: 16px;
            color: #92400e;
            font-weight: bold;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>
    <div class="health-card">
        <!-- Header -->
        <div class="header">
            <h1>PANABO CITY HEALTH OFFICE</h1>
            <h2>Official Health Card</h2>
        </div>

        <!-- Card Body -->
        <div class="card-body">
            <!-- Card Number Highlight -->
            <div class="card-number">
                <div class="card-number-label">HEALTH CARD NUMBER</div>
                <div class="card-number-value">{{ $healthCard?->card_number ?? 'N/A' }}</div>
            </div>

            <div class="content-wrapper">
                <!-- Patient Information Section -->
                <div class="patient-info">
                    @if($patient->photo_path && file_exists(public_path('storage/' . $patient->photo_path)))
                    <div class="photo-container">
                        <img src="{{ public_path('storage/' . $patient->photo_path) }}" alt="Patient Photo">
                    </div>
                    @endif

                    <div class="info-item">
                        <div class="info-label">Patient Number</div>
                        <div class="info-value">{{ $patient->patient_number }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Full Name</div>
                        <div class="info-value">{{ $patient->full_name }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Date of Birth</div>
                        <div class="info-value">{{ $patient->date_of_birth->format('F d, Y') }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Age</div>
                        <div class="info-value">{{ $patient->date_of_birth->age }} years old</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Sex</div>
                        <div class="info-value">{{ ucfirst($patient->sex ?? 'N/A') }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Blood Type</div>
                        <div class="info-value">{{ $patient->blood_type ?? 'N/A' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Barangay</div>
                        <div class="info-value">{{ $patient->barangay?->name ?? 'N/A' }}</div>
                    </div>

                    @if($patient->emergency_contact)
                    <div class="info-item">
                        <div class="info-label">Emergency Contact</div>
                        <div class="info-value">
                            @if(is_array($patient->emergency_contact))
                                {{ $patient->emergency_contact['name'] ?? '' }}
                                @if(isset($patient->emergency_contact['phone']))
                                    - {{ $patient->emergency_contact['phone'] }}
                                @endif
                            @else
                                {{ $patient->emergency_contact }}
                            @endif
                        </div>
                    </div>
                    @endif
                </div>

                <!-- QR Code Section -->
                <div class="qr-section">
                    <div class="qr-container">
                        <img src="{{ $qrCode }}" alt="QR Code" width="150" height="150">
                        <div class="qr-text">SCAN TO VERIFY</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <strong>Issued:</strong> {{ now()->format('M d, Y') }} | Valid for medical services at Panabo City Health Office | <strong>Keep this card safe</strong>
        </div>
    </div>
</body>
</html>
