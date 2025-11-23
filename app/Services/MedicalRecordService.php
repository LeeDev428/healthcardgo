<?php

namespace App\Services;

use App\Models\MedicalRecord;
use App\Services\MedicalRecordTemplates\GeneralConsultationTemplate;
use App\Services\MedicalRecordTemplates\HealthcardProcessingTemplate;
use App\Services\MedicalRecordTemplates\HIVTestingTemplate;
use App\Services\MedicalRecordTemplates\ImmunizationTemplate;
use App\Services\MedicalRecordTemplates\PrenatalCareTemplate;
use Illuminate\Support\Facades\Crypt;

class MedicalRecordService
{
    public function __construct(public NotificationService $notifications) {}

    /**
     * Available template classes
     */
    private const TEMPLATES = [
        'general' => GeneralConsultationTemplate::class,
        'healthcard' => HealthcardProcessingTemplate::class,
        'hiv' => HIVTestingTemplate::class,
        'pregnancy' => PrenatalCareTemplate::class,
        'immunization' => ImmunizationTemplate::class,
    ];

    /**
     * Get all available templates
     */
    public function getAvailableTemplates(): array
    {
        $templates = [];
        foreach (self::TEMPLATES as $key => $class) {
            $templates[] = [
                'key' => $key,
                'name' => $class::getTemplateName(),
                'category' => $class::getCategory(),
            ];
        }

        return $templates;
    }

    /**
     * Get template fields for a specific type
     */
    public function getTemplateFields(string $templateType): array
    {
        if (! isset(self::TEMPLATES[$templateType])) {
            throw new \Exception('Invalid template type');
        }

        $class = self::TEMPLATES[$templateType];

        return $class::getFields();
    }

    /**
     * Create a medical record with optional encryption
     */
    public function createMedicalRecord(array $data): MedicalRecord
    {
        $needsEncryption = in_array($data['category'], ['hiv', 'pregnancy']);

        $recordData = $data['record_data'];

        // Encrypt sensitive data if needed
        if ($needsEncryption) {
            $recordData = $this->encryptRecordData($recordData);
        }

        $record = MedicalRecord::create([
            'patient_id' => $data['patient_id'],
            'appointment_id' => $data['appointment_id'] ?? null,
            'doctor_id' => $data['doctor_id'],
            'category' => $data['category'],
            'template_type' => $data['template_type'],
            'record_data' => $recordData,
            'is_encrypted' => $needsEncryption,
            'created_by' => $data['created_by'],
        ]);

        // Notify patient of new/updated medical record
        $record->loadMissing(['patient.user']);
        if ($record->patient?->user) {
            $this->notifications->sendMedicalRecordUpdated($record);
        }

        return $record;
    }

    /**
     * Update a medical record
     */
    public function updateMedicalRecord(MedicalRecord $record, array $data): MedicalRecord
    {
        $recordData = $data['record_data'];

        // Encrypt if needed
        if ($record->is_encrypted) {
            $recordData = $this->encryptRecordData($recordData);
        }

        $updated = $record->update([
            'record_data' => $recordData,
            'updated_by' => $data['updated_by'],
        ]);

        if ($updated) {
            $record->refresh()->loadMissing(['patient.user']);
            if ($record->patient?->user) {
                $this->notifications->sendMedicalRecordUpdated($record);
            }
        }

        return $record->fresh();
    }

    /**
     * Get medical record with decrypted data if necessary
     */
    public function getMedicalRecord(MedicalRecord $record): array
    {
        $recordData = $record->record_data ?? [];

        if ($record->is_encrypted && is_array($recordData)) {
            $recordData = $this->decryptRecordData($recordData);
        }

        return [
            'id' => $record->id,
            'patient' => $record->patient,
            'doctor' => $record->doctor,
            'category' => $record->category,
            'template_type' => $record->template_type,
            'record_data' => $recordData,
            'is_encrypted' => $record->is_encrypted,
            'created_at' => $record->created_at,
            'updated_at' => $record->updated_at,
            'created_by' => $record->createdBy,
            'updated_by' => $record->updatedBy,
        ];
    }

    /**
     * Encrypt record data using AES-256
     */
    private function encryptRecordData(array $data): array
    {
        $encrypted = [];
        foreach ($data as $key => $value) {
            // Encrypt the value
            $encrypted[$key] = Crypt::encryptString(is_array($value) ? json_encode($value) : $value);
        }

        return $encrypted;
    }

    /**
     * Decrypt record data
     */
    private function decryptRecordData(array $data): array
    {
        $decrypted = [];
        foreach ($data as $key => $value) {
            try {
                $decryptedValue = Crypt::decryptString($value);
                // Try to decode JSON if it was an array
                $decoded = json_decode($decryptedValue, true);
                $decrypted[$key] = $decoded !== null ? $decoded : $decryptedValue;
            } catch (\Exception $e) {
                // If decryption fails, return the original value
                $decrypted[$key] = $value;
            }
        }

        return $decrypted;
    }

    /**
     * Check if user can access a medical record based on their role
     */
    public function canAccess(MedicalRecord $record, $user): bool
    {
        // Super Admin can access everything
        if ($user->role->name === 'super_admin') {
            return true;
        }

        // Doctors can access all records during appointments
        if ($user->role->name === 'doctor') {
            return true;
        }

        // Patients can only access their own records
        if ($user->role->name === 'patient') {
            return $record->patient_id === $user->patient?->id;
        }

        // Healthcare Admins can only access records in their category
        if ($user->role->name === 'healthcare_admin') {
            $adminCategory = $user->admin_category;

            // Medical records admin can access all non-encrypted categories
            if ($adminCategory === 'medical_records') {
                return ! $record->is_encrypted;
            }

            // Category-specific admins can only access their category
            return $record->category === $adminCategory;
        }

        return false;
    }
}
