<div class="max-w-4xl mx-auto space-y-6">
  {{-- Header --}}
  <div class="my-8">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Book an Appointment</h1>
    <p class="text-gray-600 dark:text-gray-300 mt-2">Schedule your healthcare appointment in just a few steps</p>
  </div>

  {{-- Flash Messages --}}
  @if (session('success'))
    <flux:callout variant="success">{{ session('success') }}</flux:callout>
  @endif
  @if (session('error'))
    <flux:callout variant="warning">{{ session('error') }}</flux:callout>
  @endif

  {{-- Progress Indicator --}}
  <div class="mb-8">
    <div class="flex items-center">
      @for ($i = 1; $i <= 3; $i++)
        <div class="flex items-center">
          <div
            class="flex items-center justify-center w-10 h-10 rounded-full border-2
                        {{ $step >= $i ? 'bg-blue-500 border-blue-500 text-white' : 'border-gray-300 text-gray-300' }}">
            {{ $i }}
          </div>
          @if ($i < 3)
            <div class="flex-1 h-1 mx-4 {{ $step > $i ? 'bg-blue-500' : 'bg-gray-300' }}"></div>
          @endif
        </div>
      @endfor
    </div>
    <div class="flex justify-between mt-2 text-sm text-gray-600 dark:text-gray-300">
      <span>Select Service</span>
      <span>Pick Date & Time</span>
      <span>Confirm</span>
    </div>
  </div>

  <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-8">
    {{-- Step 1: Select Service --}}
    @if ($step === 1)
      <div class="p-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Select a Service</h2>
        <div class="grid gap-4 md:grid-cols-2">
          @foreach ($services as $service)
            <div class="relative">
              <input type="radio" id="service_{{ $service->id }}" name="service" value="{{ $service->id }}"
                wire:model="selectedService" class="sr-only peer">
              <label for="service_{{ $service->id }}"
                class="flex p-4 bg-white dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-600 peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20">
                <div class="flex-1">
                  <h3 class="font-semibold text-gray-900 dark:text-white">{{ $service->name }}</h3>
                  <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">{{ $service->description }}</p>
                  <div class="flex items-center mt-3 space-x-4">
                    <span class="text-sm text-gray-500 dark:text-gray-400">
                      <flux:icon name="clock" class="w-4 h-4 inline mr-1" />
                      {{ $service->duration_minutes }} mins
                    </span>
                    @if ($service->cost > 0)
                      <span class="text-sm font-medium text-green-600">
                        ₱{{ number_format($service->cost, 2) }}
                      </span>
                    @else
                      <span class="text-sm font-medium text-green-600">Free</span>
                    @endif
                  </div>
                  @if ($service->category_name)
                    <flux:badge variant="outline" size="sm" class="mt-2">
                      {{ $service->category_name }}
                    </flux:badge>
                  @endif
                </div>
              </label>
            </div>
          @endforeach
        </div>
        @error('selectedService')
          <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
        @enderror
      </div>
    @endif

    {{-- Step 2: Pick Date & Time --}}
    @if ($step === 2)
      <div class="p-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Select Date & Time</h2>

        {{-- Date Selection - Full Calendar View --}}
        <div class="mb-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="font-medium text-gray-900 dark:text-white">Select Date</h3>
            <div class="flex items-center space-x-4">
              <flux:button variant="outline" size="sm" wire:click="previousMonth" class="flex items-center">
                <flux:icon name="chevron-left" class="w-4 h-4" />
              </flux:button>
              <h4 class="text-lg font-semibold text-gray-900 dark:text-white min-w-[140px] text-center">
                {{ \Carbon\Carbon::create($currentYear, $currentMonth, 1)->format('F Y') }}
              </h4>
              <flux:button variant="outline" size="sm" wire:click="nextMonth" class="flex items-center">
                <flux:icon name="chevron-right" class="w-4 h-4" />
              </flux:button>
            </div>
          </div>

          {{-- Calendar Grid --}}
          <div class="bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 overflow-hidden">
            {{-- Day Headers --}}
            <div class="grid grid-cols-7 bg-gray-50 dark:bg-gray-600">
              @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                <div
                  class="p-2 text-center text-sm font-medium text-gray-700 dark:text-gray-300 border-r border-gray-200 dark:border-gray-500 last:border-r-0">
                  {{ $day }}
                </div>
              @endforeach
            </div>

            {{-- Calendar Dates --}}
            <div class="grid grid-cols-7">
              @foreach ($calendarDates as $calendarDate)
                @php
                  $date = $calendarDate['date'];
                  $day = $calendarDate['day'];
                  $isCurrentMonth = $calendarDate['is_current_month'];
                  $isToday = $calendarDate['is_today'];
                  $isAvailable = $calendarDate['is_available'];
                  $isSelected = $selectedDate === $date;
                  $availableSlots = $calendarDate['available_slots'] ?? 0;
                  $isWeekend = $calendarDate['is_weekend'] ?? false;

                  $cellClasses =
                      'relative p-2 h-16 border-r border-b border-gray-200 dark:border-gray-600 last:border-r-0 transition-colors duration-150 flex flex-col items-center justify-center';

                  if (!$isCurrentMonth) {
                      $cellClasses .= ' bg-gray-50 dark:bg-gray-800 text-gray-400 dark:text-gray-500';
                  } elseif (!$isAvailable) {
                      $cellClasses .=
                          ' bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500 cursor-not-allowed';
                  } elseif ($isSelected) {
                      $cellClasses .= ' bg-blue-500 text-white cursor-pointer';
                  } else {
                      $cellClasses .=
                          ' bg-white dark:bg-gray-700 text-gray-900 dark:text-white cursor-pointer hover:bg-blue-50 dark:hover:bg-blue-900/20';
                  }

                  $dayClasses = 'flex items-center justify-center w-8 h-8 rounded-full text-sm font-medium';

                  if ($isToday && $isCurrentMonth) {
                      if ($isSelected) {
                          $dayClasses .= ' bg-white text-blue-500';
                      } elseif ($isAvailable) {
                          $dayClasses .= ' bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300';
                      } else {
                          $dayClasses .= ' bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-gray-400';
                      }
                  } elseif ($isSelected) {
                      $dayClasses .= ' bg-white text-blue-500';
                  }
                @endphp

                <div class="{{ $cellClasses }}"
                  @if ($isAvailable && $isCurrentMonth) wire:click="selectDate('{{ $date }}')" @endif>
                  <div class="{{ $dayClasses }}">
                    {{ $day }}
                  </div>

                  {{-- Available slots count --}}
                  @if ($isCurrentMonth && $isAvailable && !$isSelected)
                    <div
                      class="text-xs mt-1 {{ $availableSlots < 20 ? 'text-orange-600 dark:text-orange-400' : 'text-green-600 dark:text-green-400' }}">
                      {{ $availableSlots }} left
                    </div>
                  @elseif($isCurrentMonth && !$isAvailable && !$isWeekend && $date >= now()->toDateString())
                    <div class="text-xs mt-1 text-red-600 dark:text-red-400">
                      Full
                    </div>
                  @elseif($isCurrentMonth && $isWeekend)
                    <div class="text-xs mt-1 text-gray-500 dark:text-gray-400">
                      Closed
                    </div>
                  @elseif($isSelected)
                    <div class="text-xs mt-1 text-white">
                      Selected
                    </div>
                  @endif
                </div>
              @endforeach
            </div>
          </div>

          {{-- Legend --}}
          <div class="flex items-center justify-center flex-wrap gap-4 mt-4 text-xs text-gray-600 dark:text-gray-400">
            <div class="flex items-center space-x-1">
              <div
                class="w-3 h-3 bg-blue-100 dark:bg-blue-900 rounded-full border border-blue-300 dark:border-blue-700">
              </div>
              <span>Today</span>
            </div>
            <div class="flex items-center space-x-1">
              <div class="w-3 h-3 bg-green-600 dark:bg-green-400 rounded-full"></div>
              <span>Available (Slots left)</span>
            </div>
            <div class="flex items-center space-x-1">
              <div class="w-3 h-3 bg-orange-600 dark:bg-orange-400 rounded-full"></div>
              <span>Low Availability (&lt;20)</span>
            </div>
            <div class="flex items-center space-x-1">
              <div class="w-3 h-3 bg-red-600 dark:bg-red-400 rounded-full"></div>
              <span>Fully Booked</span>
            </div>
            <div class="flex items-center space-x-1">
              <div class="w-3 h-3 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
              <span>Weekend (Closed)</span>
            </div>
            <div class="flex items-center space-x-1">
              <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
              <span>Selected</span>
            </div>
          </div>

          @error('selectedDate')
            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
          @enderror
        </div>

        {{-- Time Selection --}}
        @if ($selectedDate && count($availableTimes) > 0)
          <div>
            <h3 class="font-medium text-gray-900 dark:text-white mb-3">Available Times</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-2">
              @foreach ($availableTimes as $time)
                <div class="relative">
                  <input type="radio" id="time_{{ $time }}" name="time" value="{{ $time }}"
                    wire:model="selectedTime" class="sr-only peer">
                  <label for="time_{{ $time }}"
                    class="flex p-3 bg-white dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-600 peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 text-center justify-center">
                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                      {{ \Carbon\Carbon::parse($time)->format('g:i A') }}
                    </span>
                  </label>
                </div>
              @endforeach
            </div>
            @error('selectedTime')
              <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
          </div>
        @elseif($selectedDate)
          <div class="text-center py-8">
            <flux:icon name="clock" class="w-12 h-12 text-gray-400 mx-auto mb-4" />
            <p class="text-gray-600 dark:text-gray-300">No available time slots for the selected date.</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Please select a different date.</p>
          </div>
        @endif
      </div>
    @endif

    {{-- Step 3: Confirmation --}}
    @if ($step === 3)
      <div class="p-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Confirm Your Appointment</h2>

        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mb-6">
          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <h3 class="font-medium text-gray-900 dark:text-white mb-2">Service</h3>
              @if ($selectedService)
                @php $service = $services->find($selectedService); @endphp
                <p class="text-gray-600 dark:text-gray-300">{{ $service->name }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $service->duration_minutes }} minutes</p>
                @if ($service->cost > 0)
                  <p class="text-sm font-medium text-green-600">₱{{ number_format($service->cost, 2) }}</p>
                @endif
              @endif
            </div>

            <div>
              <h3 class="font-medium text-gray-900 dark:text-white mb-2">Healthcare Provider</h3>
              <p class="text-gray-600 dark:text-gray-300">To be assigned by admin</p>
              <p class="text-sm text-gray-500 dark:text-gray-400">A qualified provider will be assigned to your
                appointment</p>
            </div>

            <div>
              <h3 class="font-medium text-gray-900 dark:text-white mb-2">Date & Time</h3>
              @if ($selectedDate && $selectedTime)
                <p class="text-gray-600 dark:text-gray-300">
                  {{ \Carbon\Carbon::parse($selectedDate)->format('l, F j, Y') }}
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                  {{ \Carbon\Carbon::parse($selectedTime)->format('g:i A') }}
                </p>
              @endif
            </div>

            {{-- Health Card Purpose (if applicable) --}}
            @if ($selectedService && $healthCardPurpose)
              @php
                $service = $services->find($selectedService);
              @endphp
              @if ($service && $service->category === 'health_card')
                <div>
                  <h3 class="font-medium text-gray-900 dark:text-white mb-2">Health Card Purpose</h3>
                  <p class="text-gray-600 dark:text-gray-300">
                    {{ $healthCardPurpose === 'food' ? 'Food Handler' : 'Non-Food Handler' }}
                  </p>
                  <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $healthCardPurpose === 'food' ? 'For food handling/service industries' : 'For general health clearance' }}
                  </p>
                </div>
              @endif
            @endif
          </div>
        </div>

        {{-- Health Card Purpose (Only show if service is health_card category) --}}
        @if ($selectedService)
          @php
            $service = $services->find($selectedService);
          @endphp
          @if ($service && $service->category === 'health_card')
            <div class="mb-6">
              <flux:field>
                <flux:label>Health Card Purpose <span class="text-red-500">*</span></flux:label>
                <div class="grid gap-3 md:grid-cols-2 mt-2">
                  <div class="relative">
                    <input type="radio" id="purpose_food" name="healthCardPurpose" value="food"
                      wire:model="healthCardPurpose" class="sr-only peer">
                    <label for="purpose_food"
                      class="flex p-4 bg-white dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-600 peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20">
                      <div class="flex-1">
                        <h3 class="font-semibold text-gray-900 dark:text-white">Food Handler</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">For individuals working in food
                          handling, preparation, or service industries</p>
                      </div>
                    </label>
                  </div>

                  <div class="relative">
                    <input type="radio" id="purpose_non_food" name="healthCardPurpose" value="non_food"
                      wire:model="healthCardPurpose" class="sr-only peer">
                    <label for="purpose_non_food"
                      class="flex p-4 bg-white dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-600 peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20">
                      <div class="flex-1">
                        <h3 class="font-semibold text-gray-900 dark:text-white">Non-Food Handler</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                            For individuals working in other industries or general health clearance
                        </p>
                      </div>
                    </label>
                  </div>
                </div>
                @error('healthCardPurpose')
                  <flux:error>{{ $message }}</flux:error>
                @enderror
              </flux:field>
            </div>
          @endif
        @endif

        {{-- Additional Notes --}}
        <div class="mb-6">
          <flux:field>
            <flux:label>Additional Notes (Optional)</flux:label>
            <flux:textarea wire:model="notes" placeholder="Any special requests or information..."></flux:textarea>
            @error('notes')
              <flux:error>{{ $message }}</flux:error>
            @enderror
          </flux:field>
        </div>

        {{-- Important Notice --}}
        <flux:callout type="info" class="mb-6">
          <strong>Important:</strong> A qualified healthcare provider will be assigned to your appointment by our admin
          team.
          Please arrive 15 minutes before your scheduled appointment time.
          You will receive a confirmation email with provider details and further instructions.
        </flux:callout>
      </div>
    @endif

    {{-- Navigation Buttons --}}
    <div class="flex justify-between items-center p-6 border-t border-gray-200 dark:border-gray-700">
      @if ($step > 1)
        <flux:button icon="arrow-left" variant="outline" wire:click="previousStep">
          Previous
        </flux:button>
      @else
        <div></div>
      @endif

      @if ($step < 3)
        <flux:button icon="arrow-right" variant="primary" color="blue" wire:click="nextStep">
          Next
        </flux:button>
      @else
        <flux:button icon="check" variant="primary" color="blue" wire:click="bookAppointment" wire:loading.attr="disabled" wire:target="bookAppointment">
          <span wire:loading.remove wire:target="bookAppointment">Book Appointment</span>
          <span wire:loading wire:target="bookAppointment">Booking…</span>
        </flux:button>
      @endif
    </div>
  </div>
</div>
