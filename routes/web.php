<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\HealthCardController;
use App\Http\Controllers\ReportController;
use App\Livewire\Admin\AdminDashboard;
use App\Livewire\Admin\AppointmentsList;
use App\Livewire\Admin\BarangayManagement;
use App\Livewire\Admin\DiseaseSurveillance;
use App\Livewire\Admin\ManageFeedback;
use App\Livewire\Admin\ManageHealthCardHistory;
use App\Livewire\Admin\ManageHealthCards;
use App\Livewire\Admin\ManageHistoricalData;
use App\Livewire\Admin\PatientManagement;
use App\Livewire\Admin\PendingApprovals;
use App\Livewire\Admin\Reports as AdminReports;
use App\Livewire\Admin\ScanHealthCard;
use App\Livewire\Admin\ServicesManagement;
use App\Livewire\Admin\UsersManagement;
use App\Livewire\Auth\PatientRegister;
use App\Livewire\Doctor\AppointmentsList as DoctorAppointmentsList;
use App\Livewire\Doctor\CreateMedicalRecord;
use App\Livewire\Doctor\DoctorDashboard;
use App\Livewire\Doctor\PatientsList as DoctorPatientsList;
use App\Livewire\HealthcareAdmin\AppointmentManagement;
use App\Livewire\HealthcareAdmin\HealthcareAdminDashboard;
use App\Livewire\HealthcareAdmin\PatientList;
use App\Livewire\HealthcareAdmin\RegisterPatient;
use App\Livewire\HealthcareAdmin\Reports as HealthcareAdminReports;
use App\Livewire\Home\Announcements;
use App\Livewire\Home\Homepage;
use App\Livewire\Notifications\NotificationCenter;
use App\Livewire\Patient\AppointmentDetails;
use App\Livewire\Patient\AppointmentsList as PatientAppointmentsList;
use App\Livewire\Patient\BookAppointment;
use App\Livewire\Patient\MedicalRecordDetails;
use App\Livewire\Patient\Notifications as PatientNotifications;
use App\Livewire\Patient\PatientDashboard;
use App\Livewire\Patient\Profile as PatientProfile;
use App\Livewire\Patient\SubmitFeedback;
use App\Livewire\Patient\ViewHealthCard;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

Route::get('/', Homepage::class)->name('home');
Route::get('announcements', Announcements::class)->name('announcements');

// Public Patient Registration
Route::get('register/patient', PatientRegister::class)->name('register.patient');

// Public (signed) route for appointment digital copies - scanned QR codes will point here.
Route::get('appointments/{appointment}/digital', [AppointmentController::class, 'showDigital'])
    ->name('appointments.digital')
    ->middleware('signed');

Route::get('appointments/{appointment}/digital/download', [AppointmentController::class, 'downloadDigital'])
    ->name('appointments.digital.download')
    ->middleware('signed');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', function () {
        $user = Auth::user();

        return match ($user->role_id) {
            1 => redirect()->route('admin.dashboard'),
            2 => redirect()->route('healthcare_admin.dashboard'),
            3 => redirect()->route('doctor.dashboard'),
            4 => redirect()->route('patient.dashboard'),
            default => redirect()->route('home'),
        };
    })->name('dashboard');

    // Admin Routes
    Route::prefix('admin')->middleware(['role:super_admin'])->name('admin.')->group(function () {
        Route::get('dashboard', AdminDashboard::class)->name('dashboard');
        Route::get('reports', AdminReports::class)->name('reports');
        Route::get('reports/print', [ReportController::class, 'adminPrint'])->name('reports.print');
        Route::get('users', UsersManagement::class)->name('users');
        Route::get('patients', PatientManagement::class)->name('patients');
        Route::get('appointments', AppointmentsList::class)->name('appointments');
        Route::get('services', ServicesManagement::class)->name('services');
        Route::get('barangays', BarangayManagement::class)->name('barangays');
        Route::get('approvals', PendingApprovals::class)->name('approvals');
        Route::get('health-cards', ManageHealthCards::class)->name('health-cards');
        Route::get('health-cards/{healthCard}/download-pdf', [HealthCardController::class, 'downloadPdf'])->name('health-cards.download-pdf');
        Route::get('health-cards/{healthCard}/download-png', [HealthCardController::class, 'downloadPng'])->name('health-cards.download-png');
        Route::get('health-card-history', ManageHealthCardHistory::class)->name('health-card-history');
        Route::get('scan-health-card', ScanHealthCard::class)->name('scan-health-card');
        Route::get('feedback', ManageFeedback::class)->name('feedback');
        Route::get('disease-surveillance', DiseaseSurveillance::class)->name('disease-surveillance');
        Route::get('historical-data', ManageHistoricalData::class)->name('historical-data');
    });

    // Healthcare Admin Routes (can also approve patients)
    Route::prefix('healthcare-admin')->middleware(['role:healthcare_admin'])->name('healthcare_admin.')->group(function () {
        Route::get('dashboard', HealthcareAdminDashboard::class)->name('dashboard');
        Route::get('reports', HealthcareAdminReports::class)->name('reports');
        Route::get('reports/print', [ReportController::class, 'healthcarePrint'])->name('reports.print');
        Route::get('appointments', AppointmentManagement::class)->name('appointments');
        Route::get('patients', PatientList::class)->name('patients');
        Route::get('patients/register', RegisterPatient::class)
            ->middleware('medical-records-admin')
            ->name('patients.register');
        Route::get('approvals', PendingApprovals::class)->name('approvals');
        Route::get('health-cards', ManageHealthCards::class)->name('health-cards');
    });

    // Doctor Routes
    Route::prefix('doctor')->middleware(['role:doctor'])->name('doctor.')->group(function () {
        Route::get('dashboard', DoctorDashboard::class)->name('dashboard');
        Route::get('appointments', DoctorAppointmentsList::class)->name('appointments.list');
        Route::get('medical-records/create/{appointmentId?}', CreateMedicalRecord::class)->name('medical-records.create');
        Route::get('patients', DoctorPatientsList::class)->name('patients');
        Route::get('patients/{patientId}/records', function () {
            return 'Patient records page - to be implemented';
        })->name('patients.records');
    });

    // Notifications
    Route::get('notifications', NotificationCenter::class)->name('notifications.index');

    // Patient Routes
    Route::prefix('patient')->middleware(['role:patient'])->name('patient.')->group(function () {
        Route::get('dashboard', PatientDashboard::class)->name('dashboard');
        Route::get('profile', PatientProfile::class)->name('profile');
        Route::get('appointments', PatientAppointmentsList::class)->name('appointments.list');
        Route::get('appointment/{appointment}', AppointmentDetails::class)->name('appointments.details');
        Route::get('book-appointment', BookAppointment::class)->name('book-appointment');
        Route::get('health-card', ViewHealthCard::class)->name('health-card');
        Route::get('feedback', SubmitFeedback::class)->name('feedback');
        Route::get('notifications', PatientNotifications::class)->name('notifications');
        Route::get('records/{record}', MedicalRecordDetails::class)->name('records.show');
    });

    // Settings Routes
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
});

require __DIR__.'/auth.php';
