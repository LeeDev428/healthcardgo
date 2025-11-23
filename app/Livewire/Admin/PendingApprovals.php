<?php

namespace App\Livewire\Admin;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class PendingApprovals extends Component
{
    use WithPagination;

    public $selectedUser;

    public $rejectionReason = '';

    public $showRejectModal = false;

    public function approve($userId)
    {
        $user = User::findOrFail($userId);

        $user->update([
            'status' => 'active',
            'approved_at' => now(),
            'approved_by' => Auth::id(),
        ]);

        // Create notification for patient
        Notification::notify(
            $user->id,
            'registration_approval',
            'Registration Approved',
            'Your account has been approved. You can now log in and book appointments.',
            ['approved_by' => Auth::user()->name]
        );

        session()->flash('success', 'Patient registration approved successfully.');

        $this->reset();
    }

    public function openRejectModal($userId)
    {
        $this->selectedUser = $userId;
        $this->showRejectModal = true;
    }

    public function reject()
    {
        $this->validate([
            'rejectionReason' => 'required|string|min:10',
        ]);

        $user = User::findOrFail($this->selectedUser);

        $user->update([
            'status' => 'rejected',
            'rejection_reason' => $this->rejectionReason,
            'approved_by' => Auth::id(),
        ]);

        // Create notification for patient
        Notification::notify(
            $user->id,
            'registration_rejection',
            'Registration Rejected',
            'Your registration has been rejected. Reason: '.$this->rejectionReason,
            ['rejected_by' => Auth::user()->name]
        );

        session()->flash('success', 'Patient registration rejected.');

        $this->reset();
    }

    public function render()
    {
        return view('livewire.admin.pending-approvals', [
            'pendingUsers' => User::with(['patient.barangay'])
                ->where('status', 'pending')
                ->whereHas('role', fn ($q) => $q->where('name', 'patient'))
                ->latest()
                ->paginate(10),
        ]);
    }
}
