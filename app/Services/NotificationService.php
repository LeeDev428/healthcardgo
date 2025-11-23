<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Feedback;
use App\Models\MedicalRecord;
use App\Models\Notification;
use App\Models\Patient;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;

class NotificationService
{
    /**
     * Send appointment confirmation notification
     */
    public function sendAppointmentConfirmation(Appointment $appointment): void
    {
        $this->createNotification(
            $appointment->patient->user_id,
            'appointment_confirmation',
            'Appointment Confirmed',
            "Your appointment ({$appointment->appointment_number}) has been confirmed for ".$appointment->scheduled_at->format('M d, Y \a\t g:i A'),
            [
                'appointment_id' => $appointment->id,
                'appointment_number' => $appointment->appointment_number,
                'scheduled_at' => $appointment->scheduled_at->toIso8601String(),
                'service' => $appointment->service->name,
                'queue_number' => $appointment->queue_number,
            ]
        );
    }

    /**
     * Send pending appointment notification to patient
     */
    public function sendAppointmentPending(Appointment $appointment): void
    {
        $this->createNotification(
            $appointment->patient->user_id,
            'appointment_pending',
            'Appointment Pending Approval',
            "Your appointment ({$appointment->appointment_number}) for {$appointment->service->name} has been received and is pending approval. You will be notified once confirmed.",
            [
                'appointment_id' => $appointment->id,
                'appointment_number' => $appointment->appointment_number,
                'scheduled_at' => $appointment->scheduled_at->toIso8601String(),
                'service' => $appointment->service->name,
                'queue_number' => $appointment->queue_number,
            ]
        );
    }

    /**
     * Send appointment reminder (e.g., "3 days" before)
     */
    public function sendAppointmentReminder(Appointment $appointment, string $daysBefore = '3 days'): void
    {
        // Ensure local variable accessible (explicit variable assignment avoids static analysis confusion)
        $daysBeforeMessage = $daysBefore;
        $this->createNotification(
            $appointment->patient->user_id,
            'appointment_reminder',
            'Upcoming Appointment Reminder',
            'Reminder: You have an appointment in '.$daysBeforeMessage.' on '.$appointment->scheduled_at->format('M d, Y \a\t g:i A'),
            [
                'appointment_id' => $appointment->id,
                'appointment_number' => $appointment->appointment_number,
                'scheduled_at' => $appointment->scheduled_at->toIso8601String(),
                'service' => $appointment->service->name,
                'queue_number' => $appointment->queue_number,
            ]
        );
    }

    /**
     * Send appointment cancellation notification
     */
    public function sendAppointmentCancellation(Appointment $appointment, string $reason): void
    {
        $this->createNotification(
            $appointment->patient->user_id,
            'appointment_cancellation',
            'Appointment Cancelled',
            "Your appointment ({$appointment->appointment_number}) scheduled for ".$appointment->scheduled_at->format('M d, Y').' has been cancelled.',
            [
                'appointment_id' => $appointment->id,
                'appointment_number' => $appointment->appointment_number,
                'reason' => $reason,
            ]
        );
    }

    /**
     * Send patient approval notification
     */
    public function sendPatientApproval(User $user): void
    {
        $this->createNotification(
            $user->id,
            'registration_approval',
            'Account Approved',
            'Congratulations! Your patient account has been approved. You can now book appointments and access all services.',
            [
                'approved_at' => now()->toIso8601String(),
            ]
        );
    }

    /**
     * Send patient rejection notification
     */
    public function sendPatientRejection(User $user, string $reason): void
    {
        $this->createNotification(
            $user->id,
            'registration_rejection',
            'Account Registration Rejected',
            'Your patient registration has been rejected. Reason: '.$reason,
            [
                'reason' => $reason,
                'rejected_at' => now()->toIso8601String(),
            ]
        );
    }

    /**
     * Send feedback request after completed appointment
     */
    public function sendFeedbackRequest(Appointment $appointment): void
    {
        $this->createNotification(
            $appointment->patient->user_id,
            'feedback_request',
            'Share Your Feedback',
            'How was your recent appointment? We would love to hear your feedback to improve our services.',
            [
                'appointment_id' => $appointment->id,
                'appointment_number' => $appointment->appointment_number,
                'completed_at' => $appointment->completed_at->toIso8601String(),
            ]
        );
    }

    /**
     * Send health card issued notification
     */
    public function sendHealthCardIssued(User $user, string $cardNumber): void
    {
        $this->createNotification(
            $user->id,
            'announcement',
            'Health Card Issued',
            'Your digital health card has been issued! Card Number: '.$cardNumber.'. You can now download it from your profile.',
            [
                'card_number' => $cardNumber,
                'issued_at' => now()->toIso8601String(),
            ]
        );
    }

    /**
     * Send health card expiry reminder (30 days before)
     */
    public function sendHealthCardExpiryReminder(User $user, string $cardNumber, string $expiryDate): void
    {
        $this->createNotification(
            $user->id,
            'urgent_note',
            'Health Card Expiring Soon',
            'Your health card (Card #'.$cardNumber.') will expire on '.$expiryDate.'. Please visit the health office to renew it.',
            [
                'card_number' => $cardNumber,
                'expiry_date' => $expiryDate,
            ]
        );
    }

    /**
     * Notify patient when their medical record is updated
     */
    public function sendMedicalRecordUpdated(MedicalRecord $record): void
    {
        // Guard against missing relations
        if (! $record->patient || ! $record->patient->user_id) {
            return;
        }

        $serviceName = $record->service?->name ?? 'your appointment';
        $recordedAt = $record->recorded_at?->format('M d, Y') ?? now()->format('M d, Y');

        $this->createNotification(
            $record->patient->user_id,
            'medical_record_update',
            'Medical Record Updated',
            'Your medical record for '.$serviceName.' was updated on '.$recordedAt.'.',
            [
                'medical_record_id' => $record->id,
                'appointment_id' => $record->appointment_id,
                'service' => $serviceName,
                'recorded_at' => ($record->recorded_at ?? now())->toIso8601String(),
            ]
        );
    }

    /**
     * Send new appointment notification to admin
     */
    public function sendNewAppointmentToAdmin(Appointment $appointment): void
    {
        // Get all super admins
        $admins = User::where('role_id', 1)->get();
        $patient = $appointment->patient;

        foreach ($admins as $admin) {
            $this->createNotification(
                $admin->id,
                'new_appointment',
                'New Appointment Booked',
                "A new appointment has been booked by {$patient->fullName} for {$appointment->service->name}",
                [
                    'appointment_id' => $appointment->id,
                    'appointment_number' => $appointment->appointment_number,
                    'patient_id' => $patient->id,
                    'patient_name' => $patient->fullName,
                    'patient_number' => $patient->patient_number,
                    'patient_age' => $patient->age,
                    'patient_gender' => $patient->gender,
                    'patient_blood_type' => $patient->blood_type,
                    'patient_barangay' => $patient->barangay->name ?? 'N/A',
                    'service_name' => $appointment->service->name,
                    'service_description' => $appointment->service->description,
                    'service_duration' => $appointment->service->duration_minutes,
                    'service_price' => $appointment->fee ?? 0,
                    'appointment_date' => $appointment->scheduled_at->format('M d, Y g:i A'),
                    'notes' => $appointment->notes,
                ]
            );
        }
    }

    /**
     * Send new registration notification to admin
     */
    public function sendNewRegistrationToAdmin(User $user): void
    {
        // Notify only super admins for public registrations
        $admins = User::where('role_id', 1)->get();

        foreach ($admins as $admin) {
            $this->createNotification(
                $admin->id,
                'new_patient_registration',
                'New Patient Registration',
                "A new patient registration from {$user->name} is pending approval.",
                [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                ]
            );
        }
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(int $notificationId): void
    {
        Notification::find($notificationId)?->markAsRead();
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead(int $userId): void
    {
        Notification::where('user_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * Get unread count for a user
     */
    public function getUnreadCount(int $userId): int
    {
        return Notification::where('user_id', $userId)
            ->whereNull('read_at')
            ->count();
    }

    /**
     * Get recent notifications for a user
     */
    public function getRecentNotifications(int $userId, int $limit = 10): \Illuminate\Support\Collection
    {
        return Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Delete old read notifications (older than 90 days)
     */
    public function cleanupOldNotifications(): int
    {
        return Notification::whereNotNull('read_at')
            ->where('read_at', '<', now()->subDays(90))
            ->delete();
    }

    /**
     * Create a notification
     */
    protected function createNotification(int $userId, string $type, string $title, string $message, array $data = []): Notification
    {
        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * Send notification to admins when a patient is created internally by a medical records admin
     */
    public function sendPatientCreatedByAdmin(User $patientUser, User $creator): void
    {
        // Notify super admins and healthcare admins (medical_records only)
        $admins = User::where('role_id', 1)
            ->orWhere(function ($q) {
                $q->where('role_id', 2)->where('admin_category', 'medical_records');
            })->get();

        foreach ($admins as $admin) {
            $this->createNotification(
                $admin->id,
                'patient_created',
                'New Patient Created',
                "A new patient ({$patientUser->name}) was registered by {$creator->name}.",
                [
                    'user_id' => $patientUser->id,
                    'user_name' => $patientUser->name,
                    'user_email' => $patientUser->email,
                    'created_by' => $creator->id,
                ]
            );
        }

        // Notify patient their account is active
        $this->sendPatientApproval($patientUser);
    }

    /**
     * Map a service's category to an AdminCategory slug.
     */
    protected function mapServiceToAdminCategory(?string $serviceCategory): string
    {
        return match ($serviceCategory) {
            'health_card', 'healthcard' => 'healthcard',
            'hiv_testing', 'hiv' => 'hiv',
            'pregnancy_care', 'pregnancy' => 'pregnancy',
            default => 'medical_records',
        };
    }

    /**
     * Get healthcare admins responsible for a given service (by category).
     */
    protected function getHealthcareAdminsForService(Service $service)
    {
        $category = $this->mapServiceToAdminCategory($service->category);

        return User::where('role_id', 2)
            ->where('admin_category', $category)
            ->get();
    }

    /**
     * Notify healthcare admins in the relevant category about a new appointment.
     */
    public function sendNewAppointmentToHealthcareAdmins(Appointment $appointment): void
    {
        if (! $appointment->service) {
            return;
        }

        $admins = $this->getHealthcareAdminsForService($appointment->service);
        $patient = $appointment->patient;

        foreach ($admins as $admin) {
            $this->createNotification(
                $admin->id,
                'admin_new_appointment',
                'New Appointment Booked',
                "A new appointment has been booked by {$patient->fullName} for {$appointment->service->name}",
                [
                    'appointment_id' => $appointment->id,
                    'appointment_number' => $appointment->appointment_number,
                    'patient_id' => $patient->id,
                    'patient_name' => $patient->fullName,
                    'patient_number' => $patient->patient_number,
                    'patient_age' => $patient->age,
                    'patient_gender' => $patient->gender,
                    'patient_blood_type' => $patient->blood_type,
                    'patient_barangay' => $patient->barangay->name ?? 'N/A',
                    'service_name' => $appointment->service->name,
                    'service_description' => $appointment->service->description,
                    'service_duration' => $appointment->service->duration_minutes,
                    'service_price' => $appointment->fee ?? 0,
                    'appointment_date' => $appointment->scheduled_at->format('M d, Y g:i A'),
                    'notes' => $appointment->notes,
                ]
            );
        }
    }

    /**
     * Notify healthcare admins in the relevant category about an appointment cancellation.
     */
    public function sendAppointmentCancellationToHealthcareAdmins(Appointment $appointment, string $reason): void
    {
        if (! $appointment->service) {
            return;
        }

        $admins = $this->getHealthcareAdminsForService($appointment->service);

        foreach ($admins as $admin) {
            $this->createNotification(
                $admin->id,
                'admin_appointment_cancellation',
                'Appointment Cancelled in Your Category',
                "Appointment {$appointment->appointment_number} was cancelled. Reason: {$reason}",
                [
                    'appointment_id' => $appointment->id,
                    'appointment_number' => $appointment->appointment_number,
                    'reason' => $reason,
                    'service' => $appointment->service->name,
                ]
            );
        }
    }

    /**
     * Notify super admins when feedback is received.
     */
    public function sendFeedbackReceivedToSuperAdmin(Feedback $feedback): void
    {
        $admins = User::where('role_id', 1)->get();

        foreach ($admins as $admin) {
            $this->createNotification(
                $admin->id,
                'feedback_received',
                'New Patient Feedback Received',
                'A new feedback was submitted with average rating '.number_format($feedback->averageRating(), 1).'.',
                [
                    'feedback_id' => $feedback->id,
                    'patient_id' => $feedback->patient_id,
                    'appointment_id' => $feedback->appointment_id,
                ]
            );
        }
    }

    /**
     * Notify doctor of their upcoming appointments for the selected date (default today).
     */
    public function sendDoctorDailySchedule(User $doctor, ?Carbon $date = null): void
    {
        $day = $date ? $date->copy() : now();
        $count = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('scheduled_at', $day->toDateString())
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        $this->createNotification(
            $doctor->id,
            'doctor_schedule',
            'Today\'s Appointments',
            "You have {$count} appointment(s) scheduled for ".$day->format('M d, Y').'.',
            [
                'date' => $day->toDateString(),
                'appointments_count' => $count,
            ]
        );
    }

    /**
     * Notify doctor when a patient checks in and is waiting.
     */
    public function sendPatientCheckedIn(Appointment $appointment): void
    {
        if (! $appointment->doctor_id) {
            return;
        }

        $this->createNotification(
            $appointment->doctor_id,
            'patient_checked_in',
            'Patient Checked In',
            "{$appointment->patient->fullName} has checked in and is waiting (Queue #{$appointment->queue_number}).",
            [
                'appointment_id' => $appointment->id,
                'patient_id' => $appointment->patient_id,
                'queue_number' => $appointment->queue_number,
            ]
        );
    }

    /**
     * Notify doctor about urgent patient notes.
     */
    public function sendUrgentPatientNote(User $doctor, Patient $patient, string $noteSummary): void
    {
        $this->createNotification(
            $doctor->id,
            'urgent_patient_note',
            'Urgent Patient Note',
            "Urgent note for {$patient->fullName}: {$noteSummary}",
            [
                'patient_id' => $patient->id,
            ]
        );
    }

    /**
     * Notify doctor of a medical record request.
     */
    public function sendMedicalRecordRequest(User $doctor, MedicalRecord $record): void
    {
        $this->createNotification(
            $doctor->id,
            'medical_record_request',
            'Medical Record Request',
            'A medical record requires your review or completion.',
            [
                'medical_record_id' => $record->id,
                'patient_id' => $record->patient_id,
            ]
        );
    }
}
