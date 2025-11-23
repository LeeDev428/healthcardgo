<div class="space-y-6">
    <!-- Header -->
    <div>
        <flux:heading size="xl">Service Feedback</flux:heading>
        <flux:text class="mt-2">Share your experience with Panabo City Health Office services.</flux:text>
    </div>

    <!-- Flash Messages -->
    @if(session()->has('success'))
        <flux:callout variant="success" icon="check-circle">
            {{ session('success') }}
        </flux:callout>
    @endif

    @if(session()->has('error'))
        <flux:callout variant="danger" icon="x-circle">
            {{ session('error') }}
        </flux:callout>
    @endif

    @if($hasFeedback)
        <!-- Existing Feedback Display -->
        <div class="bg-gradient-to-br from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 rounded-2xl p-8 border border-blue-200 dark:border-blue-800">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center">
                    <flux:icon name="check" class="text-white" />
                </div>
                <div>
                    <flux:heading size="lg">Thank You for Your Feedback!</flux:heading>
                    <flux:text class="text-zinc-600 dark:text-zinc-400">
                        Submitted on {{ $existingFeedback->created_at->format('M d, Y \a\t g:i A') }}
                    </flux:text>
                </div>
            </div>

            <!-- Rating Summary -->
            <div class="grid md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 text-center">
                    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400 mb-2">Overall Service</flux:text>
                    <div class="flex justify-center gap-1 mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            <flux:icon name="star" class="{{ $i <= $overall_rating ? 'text-yellow-400' : 'text-zinc-300 dark:text-zinc-600' }}" size="sm" />
                        @endfor
                    </div>
                    <flux:text weight="bold">{{ $overall_rating }}/5</flux:text>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 text-center">
                    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400 mb-2">Doctor/Staff</flux:text>
                    <div class="flex justify-center gap-1 mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            <flux:icon name="star" class="{{ $i <= $doctor_rating ? 'text-yellow-400' : 'text-zinc-300 dark:text-zinc-600' }}" size="sm" />
                        @endfor
                    </div>
                    <flux:text weight="bold">{{ $doctor_rating }}/5</flux:text>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 text-center">
                    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400 mb-2">Facility</flux:text>
                    <div class="flex justify-center gap-1 mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            <flux:icon name="star" class="{{ $i <= $facility_rating ? 'text-yellow-400' : 'text-zinc-300 dark:text-zinc-600' }}" size="sm" />
                        @endfor
                    </div>
                    <flux:text weight="bold">{{ $facility_rating }}/5</flux:text>
                </div>

                <div class="bg-white dark:bg-zinc-800 rounded-lg p-4 text-center">
                    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400 mb-2">Wait Time</flux:text>
                    <div class="flex justify-center gap-1 mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            <flux:icon name="star" class="{{ $i <= $wait_time_rating ? 'text-yellow-400' : 'text-zinc-300 dark:text-zinc-600' }}" size="sm" />
                        @endfor
                    </div>
                    <flux:text weight="bold">{{ $wait_time_rating }}/5</flux:text>
                </div>
            </div>

            <!-- Comments -->
            @if($comments)
                <div class="bg-white dark:bg-zinc-800 rounded-lg p-4">
                    <flux:text weight="semibold" class="mb-2">Your Comments:</flux:text>
                    <flux:text class="text-zinc-600 dark:text-zinc-400">{{ $comments }}</flux:text>
                </div>
            @endif

            <!-- Recommendation -->
            <div class="mt-4 flex items-center gap-2">
                @if($would_recommend)
                    <flux:badge variant="success" size="lg">
                        <flux:icon name="thumb-up" size="sm" /> Would Recommend
                    </flux:badge>
                @else
                    <flux:badge variant="warning" size="lg">
                        <flux:icon name="thumb-down" size="sm" /> Would Not Recommend
                    </flux:badge>
                @endif
            </div>

            <!-- Admin Response -->
            @if($existingFeedback->admin_response)
                <div class="mt-6 bg-blue-100 dark:bg-blue-900/30 rounded-lg p-4 border-l-4 border-blue-600">
                    <div class="flex items-center gap-2 mb-2">
                        <flux:icon name="chat-bubble-left-right" class="text-blue-600 dark:text-blue-400" />
                        <flux:text weight="semibold" class="text-blue-900 dark:text-blue-100">Admin Response</flux:text>
                    </div>
                    <flux:text class="text-blue-800 dark:text-blue-200">{{ $existingFeedback->admin_response }}</flux:text>
                    <flux:text size="xs" class="text-blue-600 dark:text-blue-400 mt-2">
                        Responded on {{ $existingFeedback->responded_at->format('M d, Y') }}
                    </flux:text>
                </div>
            @endif
        </div>

        <flux:callout variant="info" icon="information-circle">
            You can only submit feedback once. If you need to update your feedback or have additional concerns, please contact the health office directly.
        </flux:callout>
    @else
        <!-- Feedback Form -->
        <form wire:submit="submitFeedback">
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-6 space-y-8">
                <!-- Rating Section 1: Overall Service -->
                <div>
                    <flux:heading size="md" class="mb-2">Overall Service Quality</flux:heading>
                    <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400 mb-4">
                        How would you rate your overall experience with our health services?
                    </flux:text>

                    <div class="flex gap-2">
                        @for($i = 1; $i <= 5; $i++)
                            <button
                                type="button"
                                wire:click="setRating('overall_rating', {{ $i }})"
                                class="group transition-transform hover:scale-110">
                                <flux:icon
                                    name="star"
                                    size="xl"
                                    class="{{ $i <= $overall_rating ? 'text-yellow-400' : 'text-zinc-300 dark:text-zinc-600 group-hover:text-yellow-200' }}"
                                />
                            </button>
                        @endfor
                        @if($overall_rating > 0)
                            <span class="ml-3 text-lg font-semibold text-zinc-700 dark:text-zinc-300">{{ $overall_rating }}/5</span>
                        @endif
                    </div>
                    @error('overall_rating')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </div>

                <!-- Rating Section 2: Doctor/Staff -->
                <div>
                    <flux:heading size="md" class="mb-2">Doctor & Staff Professionalism</flux:heading>
                    <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400 mb-4">
                        How professional and helpful were the doctors and staff?
                    </flux:text>

                    <div class="flex gap-2">
                        @for($i = 1; $i <= 5; $i++)
                            <button
                                type="button"
                                wire:click="setRating('doctor_rating', {{ $i }})"
                                class="group transition-transform hover:scale-110">
                                <flux:icon
                                    name="star"
                                    size="xl"
                                    class="{{ $i <= $doctor_rating ? 'text-yellow-400' : 'text-zinc-300 dark:text-zinc-600 group-hover:text-yellow-200' }}"
                                />
                            </button>
                        @endfor
                        @if($doctor_rating > 0)
                            <span class="ml-3 text-lg font-semibold text-zinc-700 dark:text-zinc-300">{{ $doctor_rating }}/5</span>
                        @endif
                    </div>
                    @error('doctor_rating')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </div>

                <!-- Rating Section 3: Facility -->
                <div>
                    <flux:heading size="md" class="mb-2">Facility Cleanliness & Comfort</flux:heading>
                    <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400 mb-4">
                        How would you rate the cleanliness and comfort of our facility?
                    </flux:text>

                    <div class="flex gap-2">
                        @for($i = 1; $i <= 5; $i++)
                            <button
                                type="button"
                                wire:click="setRating('facility_rating', {{ $i }})"
                                class="group transition-transform hover:scale-110">
                                <flux:icon
                                    name="star"
                                    size="xl"
                                    class="{{ $i <= $facility_rating ? 'text-yellow-400' : 'text-zinc-300 dark:text-zinc-600 group-hover:text-yellow-200' }}"
                                />
                            </button>
                        @endfor
                        @if($facility_rating > 0)
                            <span class="ml-3 text-lg font-semibold text-zinc-700 dark:text-zinc-300">{{ $facility_rating }}/5</span>
                        @endif
                    </div>
                    @error('facility_rating')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </div>

                <!-- Rating Section 4: Wait Time -->
                <div>
                    <flux:heading size="md" class="mb-2">Wait Time & Efficiency</flux:heading>
                    <flux:text size="sm" class="text-zinc-600 dark:text-zinc-400 mb-4">
                        How satisfied were you with the wait time and service efficiency?
                    </flux:text>

                    <div class="flex gap-2">
                        @for($i = 1; $i <= 5; $i++)
                            <button
                                type="button"
                                wire:click="setRating('wait_time_rating', {{ $i }})"
                                class="group transition-transform hover:scale-110">
                                <flux:icon
                                    name="star"
                                    size="xl"
                                    class="{{ $i <= $wait_time_rating ? 'text-yellow-400' : 'text-zinc-300 dark:text-zinc-600 group-hover:text-yellow-200' }}"
                                />
                            </button>
                        @endfor
                        @if($wait_time_rating > 0)
                            <span class="ml-3 text-lg font-semibold text-zinc-700 dark:text-zinc-300">{{ $wait_time_rating }}/5</span>
                        @endif
                    </div>
                    @error('wait_time_rating')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </div>

                <!-- Recommendation -->
                <div class="pt-6 border-t border-zinc-200 dark:border-zinc-700">
                    <flux:field>
                        <flux:label>Would you recommend our services to others?</flux:label>
                        <div class="flex gap-4 mt-3">
                            <flux:radio wire:model="would_recommend" value="1" label="Yes, I would recommend" />
                            <flux:radio wire:model="would_recommend" value="0" label="No, I would not recommend" />
                        </div>
                    </flux:field>
                </div>

                <!-- Comments -->
                <div>
                    <flux:field>
                        <flux:label>Additional Comments (Optional)</flux:label>
                        <flux:textarea
                            wire:model="comments"
                            placeholder="Share any additional thoughts, suggestions, or concerns about your experience..."
                            rows="5"
                        />
                        <flux:text size="xs" class="text-zinc-500 dark:text-zinc-400 mt-1">
                            Maximum 1000 characters
                        </flux:text>
                        @error('comments')
                            <flux:error>{{ $message }}</flux:error>
                        @enderror
                    </flux:field>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end gap-3 pt-6 border-t border-zinc-200 dark:border-zinc-700">
                    <flux:button icon="paper-airplane" type="submit" variant="primary" color="blue">
                        Submit Feedback
                    </flux:button>
                </div>
            </div>
        </form>

        <flux:callout variant="info" icon="information-circle">
            <strong>Note:</strong> You can only submit feedback once. Please ensure all ratings and comments accurately reflect your experience before submitting.
        </flux:callout>
    @endif
</div>
