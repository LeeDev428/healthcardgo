<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    public function __construct(public ReportService $reports) {}

    /**
     * Generate a PDF for Super Admin reports.
     */
    public function adminPrint(Request $request): Response
    {
        $payload = $this->buildDataset($request);

        $pdf = Pdf::loadView('reports.print', $payload)->setPaper('a4', 'portrait');

        return $pdf->stream($this->filename($payload['title']));
    }

    /**
     * Generate a PDF for Healthcare Admin reports (scoped to their category when applicable).
     */
    public function healthcarePrint(Request $request): Response
    {
        $payload = $this->buildDataset($request, true);

        $pdf = Pdf::loadView('reports.print', $payload)->setPaper('a4', 'portrait');

        return $pdf->stream($this->filename($payload['title']));
    }

    /**
     * Prepare dataset for a given request.
     *
     * @return array<string,mixed>
     */
    protected function buildDataset(Request $request, bool $forHealthcareAdmin = false): array
    {
        $type = $request->string('type', 'appointments')->toString();
        $filters = [
            'from' => $request->input('from'),
            'to' => $request->input('to'),
            'status' => $request->input('status'),
            'doctor_id' => $request->integer('doctor_id') ?: null,
            'service_category' => $request->input('service_category'),
            'disease_type' => $request->input('disease_type'),
            'barangay_id' => $request->integer('barangay_id') ?: null,
        ];

        $viewer = Auth::user();

        $dataset = match ($type) {
            'diseases' => $this->reports->getDiseasesReport($filters, $viewer),
            'feedback' => $this->reports->getFeedbackReport($filters, $viewer),
            default => $this->reports->getAppointmentsReport($filters, $viewer),
        };

        $title = match ($type) {
            'diseases' => 'Disease Cases Report',
            'feedback' => 'Patient Feedback Report',
            default => 'Appointments Report',
        };

        return [
            'title' => $title,
            'type' => $type,
            'filters' => $filters,
            'dataset' => $dataset,
            'viewer' => $viewer,
        ];
    }

    protected function filename(string $title): string
    {
        return str($title.' '.now()->format('Y-m-d'))->slug().' .pdf';
    }
}
