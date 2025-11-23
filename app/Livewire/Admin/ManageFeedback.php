<?php

namespace App\Livewire\Admin;

use App\Models\Feedback;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Manage Feedback')]
class ManageFeedback extends Component
{
    use WithPagination;

    public $search = '';

    public $filterRating = '';

    public $filterRecommendation = '';

    public $showResponseModal = false;

    public $selectedFeedback = null;

    public $adminResponse = '';

    public function viewFeedback(Feedback $feedback)
    {
        $this->selectedFeedback = $feedback;
        $this->adminResponse = $feedback->admin_response ?? '';
        $this->showResponseModal = true;
    }

    public function closeModal()
    {
        $this->showResponseModal = false;
        $this->selectedFeedback = null;
        $this->adminResponse = '';
        $this->resetValidation();
    }

    public function submitResponse()
    {
        $this->validate([
            'adminResponse' => 'required|string|max:1000',
        ]);

        try {
            $this->selectedFeedback->update([
                'admin_response' => $this->adminResponse,
                'responded_by' => Auth::id(),
                'responded_at' => now(),
            ]);

            session()->flash('success', 'Response submitted successfully!');
            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to submit response. Please try again.');
        }
    }

    public function render()
    {
        $query = Feedback::with(['patient.user', 'respondedBy'])
            ->when($this->search, function ($q) {
                $q->whereHas('patient.user', function ($userQuery) {
                    $userQuery->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->filterRating, function ($q) {
                $q->where('overall_rating', '>=', $this->filterRating);
            })
            ->when($this->filterRecommendation !== '', function ($q) {
                $q->where('would_recommend', $this->filterRecommendation);
            })
            ->latest();

        $feedback = $query->paginate(15);

        // Calculate statistics
        $stats = [
            'total' => Feedback::count(),
            'average_rating' => round(Feedback::avg('overall_rating'), 1),
            'would_recommend_percentage' => Feedback::count() > 0
                ? round((Feedback::where('would_recommend', true)->count() / Feedback::count()) * 100)
                : 0,
            'pending_responses' => Feedback::whereNull('admin_response')->count(),
        ];

        return view('livewire.admin.manage-feedback', [
            'feedback' => $feedback,
            'stats' => $stats,
        ]);
    }
}
