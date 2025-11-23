<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AppointmentController extends Controller
{
    /**
     * Show the digital copy of an appointment.
     * This route should be signed and (optionally) temporary.
     */
    public function showDigital(Request $request, Appointment $appointment)
    {
        // Signed middleware ensures the URL is valid. Render a simple view with appointment details.
        return view('appointments.digital_copy', ['appointment' => $appointment]);
    }

    /**
     * Download the digital copy PDF of an appointment.
     * This route should be signed and (optionally) temporary.
     */
    public function downloadDigital(Request $request, Appointment $appointment)
    {
        if (! $appointment->digital_copy_path || ! Storage::disk('public')->exists($appointment->digital_copy_path)) {
            abort(404, 'Digital copy not found.');
        }

        return response()->download(
            Storage::disk('public')->path($appointment->digital_copy_path),
            'appointment-'.$appointment->appointment_number.'.pdf'
        );
    }
}
