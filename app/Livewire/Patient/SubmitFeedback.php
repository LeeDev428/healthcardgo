<?php

namespace App\Livewire\Patient;

use App\Models\Feedback;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.patient')]
#[Title('Submit Feedback')]
class SubmitFeedback extends Component
{
    public $patient;

    public $existingFeedback = null;

    public $hasFeedback = false;

    // Form fields
    public $overall_rating = 0;

    public $doctor_rating = 0;

    public $facility_rating = 0;

    public $wait_time_rating = 0;

    public $would_recommend = true;

    public $comments = '';

    protected $rules = [
        'overall_rating' => 'required|integer|min:1|max:5',
        'doctor_rating' => 'required|integer|min:1|max:5',
        'facility_rating' => 'required|integer|min:1|max:5',
        'wait_time_rating' => 'required|integer|min:1|max:5',
        'would_recommend' => 'required|boolean',
        'comments' => 'nullable|string|max:1000',
    ];

    public function mount()
    {
        $this->patient = Auth::user()->patient;

        if (! $this->patient) {
            abort(404, 'Patient profile not found');
        }

        // Check if patient has already submitted feedback
        $this->existingFeedback = Feedback::where('patient_id', $this->patient->id)->first();
        $this->hasFeedback = $this->existingFeedback !== null;

        // If feedback exists, populate form for viewing
        if ($this->hasFeedback) {
            $this->overall_rating = $this->existingFeedback->overall_rating;
            $this->doctor_rating = $this->existingFeedback->doctor_rating;
            $this->facility_rating = $this->existingFeedback->facility_rating;
            $this->wait_time_rating = $this->existingFeedback->wait_time_rating;
            $this->would_recommend = $this->existingFeedback->would_recommend;
            $this->comments = $this->existingFeedback->comments;
        }
    }

    public function setRating($field, $value)
    {
        if (! $this->hasFeedback) {
            $this->$field = $value;
        }
    }

    public function submitFeedback()
    {
        if ($this->hasFeedback) {
            session()->flash('error', 'You have already submitted feedback.');

            return;
        }

        $this->validate();

        try {
            Feedback::create([
                'patient_id' => $this->patient->id,
                'appointment_id' => null, // Not tied to specific appointment
                'overall_rating' => $this->overall_rating,
                'doctor_rating' => $this->doctor_rating,
                'facility_rating' => $this->facility_rating,
                'wait_time_rating' => $this->wait_time_rating,
                'would_recommend' => $this->would_recommend,
                'comments' => $this->comments,
            ]);

            $this->hasFeedback = true;
            session()->flash('success', 'Thank you for your feedback! Your input helps us improve our services.');

            // Reload feedback
            $this->mount();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to submit feedback. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.patient.submit-feedback');
    }
}
