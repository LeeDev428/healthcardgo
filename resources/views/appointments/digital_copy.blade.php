<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Appointment {{ $appointment->appointment_number }} - Digital Copy</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #222; }
        .container { max-width: 800px; margin: 0 auto; padding: 24px; }
        .header { text-align: center; margin-bottom: 12px; }
        .meta { margin-top: 12px; }
        .meta dt { font-weight: bold; }
        .meta dd { margin: 0 0 8px 0; }
    </style>
    </head>
<body>
    <div class="container">
        <div class="header">
            <h1>Appointment Digital Copy</h1>
            <p>Appointment #: <strong>{{ $appointment->appointment_number }}</strong></p>
        </div>

        <dl class="meta">
            <dt>Patient</dt>
            <dd>{{ optional($appointment->patient->user)->name ?? 'N/A' }}</dd>

            <dt>Service</dt>
            <dd>{{ optional($appointment->service)->name ?? 'N/A' }}</dd>

            <dt>Doctor</dt>
            <dd>{{ optional($appointment->doctor->user ?? null)->name ?? optional($appointment->doctor)->name ?? 'Unassigned' }}</dd>

            <dt>Scheduled At</dt>
            <dd>{{ $appointment->scheduled_at?->format('Y-m-d H:i') ?? 'N/A' }}</dd>

            <dt>Queue Number</dt>
            <dd>{{ $appointment->queue_number }}</dd>

            <dt>Status</dt>
            <dd>{{ ucfirst($appointment->status) }}</dd>

            @if($appointment->notes)
            <dt>Notes</dt>
            <dd>{{ $appointment->notes }}</dd>
            @endif
        </dl>

        @if($appointment->qr_code_path)
            <div style="margin-top:20px; text-align:center;">
                <img src="{{ asset('storage/'.$appointment->qr_code_path) }}" alt="QR Code" style="width:200px;height:200px;" />
                <p style="font-size:12px; color:#666;">Scan to view this digital copy online.</p>
            </div>
        @endif
    </div>
</body>
</html>
