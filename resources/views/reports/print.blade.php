<!doctype html>
<html>
<head>
    <meta charset="utf-8" />
    <title>{{ $title }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 12px; color: #111827; }
        .container { width: 100%; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
        .title { font-size: 20px; font-weight: bold; }
        .muted { color: #6b7280; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; }
        th { background: #f3f4f6; font-weight: 600; }
        .summary { display: flex; gap: 12px; margin: 12px 0; }
        .card { border: 1px solid #e5e7eb; padding: 10px; border-radius: 6px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="title">{{ $title }}</div>
            <div class="muted">Generated: {{ now()->format('Y-m-d H:i') }}</div>
        </div>

        <div class="muted">Range: {{ $dataset['meta']['from'] ?? '' }} to {{ $dataset['meta']['to'] ?? '' }}</div>

        <div class="summary">
            <div class="card">Total: <strong>{{ $dataset['meta']['total'] ?? 0 }}</strong></div>
            @if(($dataset['breakdown']['status'] ?? null) && $type === 'appointments')
                <div class="card">By Status:
                    @foreach($dataset['breakdown']['status'] as $k => $v)
                        <div>{{ ucfirst(str_replace('_',' ', $k)) }}: <strong>{{ $v }}</strong></div>
                    @endforeach
                </div>
            @endif
            @if(($dataset['averages'] ?? null) && $type === 'feedback')
                <div class="card">Averages:
                    <div>Overall: <strong>{{ $dataset['averages']['overall'] }}</strong></div>
                    <div>Doctor: <strong>{{ $dataset['averages']['doctor'] }}</strong></div>
                    <div>Facility: <strong>{{ $dataset['averages']['facility'] }}</strong></div>
                    <div>Wait Time: <strong>{{ $dataset['averages']['wait_time'] }}</strong></div>
                </div>
            @endif
        </div>

        <table>
            <thead>
                <tr>
                    @if($type === 'appointments')
                        <th>Number</th>
                        <th>Scheduled</th>
                        <th>Status</th>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Service</th>
                    @elseif($type === 'diseases')
                        <th>Type</th>
                        <th>Patient</th>
                        <th>Barangay</th>
                        <th>Diagnosis Date</th>
                        <th>Status</th>
                    @else
                        <th>Patient</th>
                        <th>Appointment</th>
                        <th>Overall</th>
                        <th>Comments</th>
                        <th>Submitted</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach(($dataset['list'] ?? []) as $row)
                    <tr>
                        @if($type === 'appointments')
                            <td>{{ $row['number'] }}</td>
                            <td>{{ $row['scheduled_at'] }}</td>
                            <td>{{ ucfirst(str_replace('_',' ', $row['status'])) }}</td>
                            <td>{{ $row['patient'] }}</td>
                            <td>{{ $row['doctor'] }}</td>
                            <td>{{ $row['service'] }}</td>
                        @elseif($type === 'diseases')
                            <td>{{ $row['disease_type'] }}</td>
                            <td>{{ $row['patient'] }}</td>
                            <td>{{ $row['barangay'] }}</td>
                            <td>{{ $row['diagnosis_date'] }}</td>
                            <td>{{ ucfirst($row['status']) }}</td>
                        @else
                            <td>{{ $row['patient'] }}</td>
                            <td>#{{ $row['appointment'] }}</td>
                            <td>{{ $row['overall_rating'] }}</td>
                            <td>{{ $row['comments'] }}</td>
                            <td>{{ $row['created_at'] }}</td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
