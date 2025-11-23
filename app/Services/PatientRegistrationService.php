<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PatientRegistrationService
{
    /**
     * Register a patient (public or internal)
     *
     * @param  array{ name:string, email:string, password:string, contact_number:string, date_of_birth:string, gender:string, barangay_id:int|string, blood_type?:string|null, emergency_contact_name:string, emergency_contact_number:string, allergies?:string|null, current_medications?:string|null, medical_history?:string|null, photo?:\Livewire\Features\SupportFileUploads\TemporaryUploadedFile|null }  $data
     */
    public function register(array $data, ?User $creator = null, bool $internal = false): User
    {
        return DB::transaction(function () use ($data, $creator, $internal) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'contact_number' => $data['contact_number'],
                'role_id' => Role::where('name', 'patient')->first()->id,
                'status' => $internal ? 'active' : 'pending',
                'approved_at' => $internal ? now() : null,
                'approved_by' => $internal ? ($creator?->id) : null,
            ]);

            $photoPath = null;
            if (! empty($data['photo'])) {
                $photoPath = $data['photo']->store('patient-photos', 'public');
            }

            Patient::create([
                'user_id' => $user->id,
                'date_of_birth' => $data['date_of_birth'],
                'gender' => $data['gender'],
                'barangay_id' => $data['barangay_id'],
                'blood_type' => $data['blood_type'] ?? null,
                'photo_path' => $photoPath,
                'emergency_contact' => [
                    'name' => $data['emergency_contact_name'],
                    'number' => $data['emergency_contact_number'],
                ],
                'allergies' => ! empty($data['allergies']) ? [$data['allergies']] : null,
                'current_medications' => ! empty($data['current_medications']) ? [$data['current_medications']] : null,
                'medical_history' => ! empty($data['medical_history']) ? [$data['medical_history']] : null,
            ]);

            return $user;
        });
    }
}
