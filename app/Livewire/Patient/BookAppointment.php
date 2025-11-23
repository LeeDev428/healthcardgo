<?php

namespace App\Livewire\Patient;

use App\Models\Service;
use App\Services\AppointmentService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.patient')]
#[Title('Book Appointment')]
class BookAppointment extends Component
{
    public $step = 1;

    public $selectedService = null;

    public $selectedDate = null;

    public $selectedTime = null;

    public $notes = '';

    public $healthCardPurpose = null;

    public $services = [];

    public $availableDates = [];

    public $availableTimes = [];

    public $currentMonth;

    public $currentYear;

    public $calendarDates = [];

    public $unavailableDates = [];

    protected $rules = [
        'selectedService' => 'required|exists:services,id',
        'selectedDate' => 'required|date|after:now',
        'selectedTime' => 'required',
        'notes' => 'nullable|string|max:500',
        'healthCardPurpose' => 'nullable|string|in:food,non_food',
    ];

    public function mount()
    {
        $this->services = Service::active()->get();
        $this->currentMonth = Carbon::now()->month;
        $this->currentYear = Carbon::now()->year;
    }

    public function nextStep()
    {
        $this->validateCurrentStep();

        if ($this->step < 3) {
            $this->step++;

            // Load data for next step
            switch ($this->step) {
                case 2:
                    // Regenerate calendar based on the selected service and clear any previous selections
                    $this->generateCalendar();
                    $this->clearSelectedDateTime();
                    $this->generateAvailableTimes();
                    break;
            }
        }
    }

    public function previousStep()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function validateCurrentStep()
    {
        switch ($this->step) {
            case 1:
                $this->validate(['selectedService' => 'required|exists:services,id']);

                // Check if patient already has an active appointment for this service
                $user = Auth::user();
                $patient = $user->patient;

                if ($patient) {
                    $existingAppointment = \App\Models\Appointment::where('patient_id', $patient->id)
                        ->where('service_id', $this->selectedService)
                        ->whereIn('status', ['pending', 'confirmed', 'checked_in', 'in_progress'])
                        ->exists();

                    if ($existingAppointment) {
                        $this->addError('selectedService', 'You already have an active appointment for this service. Please cancel or complete it before booking a new one.');

                        return;
                    }
                }
                break;
            case 2:
                $this->validate([
                    'selectedDate' => 'required|date|after:'.Carbon::now()->toDateString(),
                    'selectedTime' => 'required',
                ]);
                break;
        }
    }

    public function generateCalendar()
    {
        $this->calendarDates = [];
        $this->unavailableDates = [];

        // Create a Carbon instance for the first day of the current month
        $firstDay = Carbon::create($this->currentYear, $this->currentMonth, 1);
        $lastDay = $firstDay->copy()->endOfMonth();

        // Get the first day of the calendar (Sunday) - might be from previous month
        $calendarStart = $firstDay->copy()->startOfWeek(Carbon::SUNDAY);

        // Get the last day of the calendar (Saturday) - might be from next month
        $calendarEnd = $lastDay->copy()->endOfWeek(Carbon::SATURDAY);

        // Generate all dates for the calendar grid
        $current = $calendarStart->copy();
        while ($current->lte($calendarEnd)) {
            $dateString = $current->toDateString();
            $isAvailable = $this->isDateAvailable($current);

            $this->calendarDates[] = [
                'date' => $dateString,
                'day' => $current->day,
                'is_current_month' => $current->month === $this->currentMonth,
                'is_today' => $current->isToday(),
                'is_weekend' => $current->isWeekend(),
                'is_available' => $isAvailable,
                'available_slots' => $isAvailable ? $this->getAvailableSlots($current) : 0,
                'carbon' => $current->copy(),
            ];
            $current->addDay();
        }
    }

    public function isDateAvailable(Carbon $date): bool
    {
        if (! $this->selectedService) {
            return false;
        }

        // Cannot book appointments in the past
        if ($date->lt(Carbon::now()->startOfDay())) {
            return false;
        }

        $appointmentService = app(AppointmentService::class);

        // Check 7-day minimum lead time (must be at least 7 days from now)
        if (! $appointmentService->isWithinBookingWindow($date)) {
            return false;
        }

        // No bookings on weekends (Saturday and Sunday)
        if ($date->isWeekend()) {
            return false;
        }

        // Check if daily capacity (100 appointments per day per service) is available
        if (! $appointmentService->isDailyCapacityAvailable($date, (int) $this->selectedService)) {
            return false;
        }

        // Check if there are available slots for this specific service
        $slots = $appointmentService->getAvailableSlots($date, $this->selectedService);

        return $slots['available_slots'] > 0;
    }

    public function getAvailableSlots(Carbon $date): int
    {
        if (! $this->selectedService) {
            return 0;
        }

        $appointmentService = app(AppointmentService::class);
        $slots = $appointmentService->getAvailableSlots($date, $this->selectedService);

        return $slots['available_slots'];
    }

    public function nextMonth()
    {
        if ($this->currentMonth === 12) {
            $this->currentMonth = 1;
            $this->currentYear++;
        } else {
            $this->currentMonth++;
        }

        // Don't allow navigation beyond 2 months from now
        $maxDate = Carbon::now()->addMonths(2);
        $selectedDate = Carbon::create($this->currentYear, $this->currentMonth, 1);

        if ($selectedDate->gt($maxDate)) {
            $this->currentMonth = $maxDate->month;
            $this->currentYear = $maxDate->year;
        }

        $this->calendarDates = []; // Clear cached data
        $this->generateCalendar();
        $this->clearSelectedDateTime();
    }

    public function previousMonth()
    {
        if ($this->currentMonth === 1) {
            $this->currentMonth = 12;
            $this->currentYear--;
        } else {
            $this->currentMonth--;
        }

        // Don't allow navigation before current month
        $minDate = Carbon::now();
        $selectedDate = Carbon::create($this->currentYear, $this->currentMonth, 1);

        if ($selectedDate->lt($minDate->startOfMonth())) {
            $this->currentMonth = $minDate->month;
            $this->currentYear = $minDate->year;
        }

        $this->calendarDates = []; // Clear cached data
        $this->generateCalendar();
        $this->clearSelectedDateTime();
    }

    public function selectDate($date)
    {
        // Find the date in our calendar
        $calendarDate = collect($this->calendarDates)->firstWhere('date', $date);

        if ($calendarDate && $calendarDate['is_available'] && $calendarDate['is_current_month']) {
            $this->selectedDate = $date;
            $this->selectedTime = null;
            $this->generateAvailableTimes();
        }
    }

    private function clearSelectedDateTime()
    {
        $this->selectedDate = null;
        $this->selectedTime = null;
        $this->availableTimes = [];
    }

    public function generateAvailableDates()
    {
        // This method is now replaced by generateCalendar()
        // Keeping for backward compatibility if needed
        $this->generateCalendar();
    }

    public function updatedSelectedService(): void
    {
        // When service changes, refresh calendar availability and clear date/time selection
        $this->calendarDates = []; // Clear old data
        $this->generateCalendar();
        $this->clearSelectedDateTime();
    }

    public function generateAvailableTimes()
    {
        $this->availableTimes = [];

        if (! $this->selectedDate) {
            return;
        }

        // Generate standard business hours time slots (8 AM to 5 PM, 30-minute intervals)
        // As per PRD: Operating hours: 8:00 AM – 5:00 PM, Monday to Friday
        $startTime = Carbon::parse($this->selectedDate.' 08:00');
        $endTime = Carbon::parse($this->selectedDate.' 17:00');

        while ($startTime->lt($endTime)) {
            $this->availableTimes[] = $startTime->format('H:i');
            // Move to next 30-minute slot (18 slots per day)
            $startTime->addMinutes(30);
        }
    }

    public function bookAppointment()
    {
        // Validate health_card_purpose if service is health_card category
        $service = Service::find($this->selectedService);
        if ($service && $service->category === 'health_card') {
            $this->rules['healthCardPurpose'] = 'required|string|in:food,non_food';
        }

        $this->validate();

        $user = Auth::user();
        $patient = $user->patient;

        if (! $patient) {
            session()->flash('error', 'Patient profile not found. Please contact administrator.');

            return;
        }

        $scheduledAt = Carbon::parse($this->selectedDate.' '.$this->selectedTime);

        try {
            $appointmentService = app(AppointmentService::class);
            $appointment = $appointmentService->bookAppointment([
                'patient_id' => $patient->id,
                'doctor_id' => null, // Will be assigned by admin later
                'service_id' => $this->selectedService,
                'scheduled_at' => $scheduledAt,
                'notes' => $this->notes,
                'health_card_purpose' => $this->healthCardPurpose,
                'fee' => 0,
            ]);

            session()->flash('success', 'Appointment booked successfully! Your appointment number is: '.$appointment->appointment_number.'. Queue number: '.$appointment->queue_number.'. A healthcare provider will be assigned by our admin team.');

            return redirect()->route('patient.dashboard');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function updatedSelectedDate()
    {
        $this->selectedTime = null;
        $this->generateAvailableTimes();
    }

    public function render()
    {
        return view('livewire.patient.book-appointment');
    }
}
