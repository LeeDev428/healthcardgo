<?php

namespace App\Livewire\Patient;

use App\Models\MedicalRecord;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.patient')]
#[Title('Medical Record Details')]
class MedicalRecordDetails extends Component
{
    public MedicalRecord $medicalRecord;

    public function mount(MedicalRecord $record): void
    {
        // Authorize: ensure the record belongs to the authenticated patient
        $userId = (int) Auth::id();
        $ownerId = (int) ($record->patient?->user_id ?? 0);

        if ($userId === 0 || $ownerId !== $userId) {
            abort(404);
        }

        $this->medicalRecord = $record->load(['service', 'doctor']);
    }

    public function render(): ViewContract
    {
        return view('livewire.patient.medical-record-details');
    }
}
