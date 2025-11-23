<flux:modal name="healthcard-modal" :dismissible="false" :closable="false">
    <div class="space-y-6">
        <div>
            <h2 class="font-bold text-center text-cyan-600 text-2xl">{{ __('app.healthcard_service_title') }}</h2>
        </div>

        <div>
            <h2 class="font-semibold text-xl">{{ __('app.healthcard_what_title') }}</h2>
            <p class="text-gray-600 dark:text-white">
                {{ __('app.healthcard_what_description') }}
            </p>

            <h2 class="font-semibold text-xl text-[#0065c0] mt-4">{{ __('app.healthcard_who_title') }}</h2>
            <ul class="text-gray-600 dark:text-white space-y-2 list-disc list-inside mt-2">
                <li>
                    {!! __('app.healthcard_who_item1') !!}
                </li>
                <li>
                    {!! __('app.healthcard_who_item2') !!}
                </li>
            </ul>

            <h2 class="font-semibold text-xl text-[#0065c0] mt-4">{{ __('app.healthcard_types_title') }}</h2>
            <ul class="text-gray-600 dark:text-white space-y-2 list-disc list-inside mt-2">
                <li>
                    {!! __('app.healthcard_types_item1') !!}
                </li>
                <li>
                    {!! __('app.healthcard_types_item2') !!}
                </li>
            </ul>

            <h2 class="font-semibold text-xl text-[#0065c0] mt-4">{{ __('app.healthcard_important_title') }}</h2>
            <ul class="text-gray-600 dark:text-white space-y-2 list-disc list-inside mt-2">
                <li>{{ __('app.healthcard_important_item1') }}</li>
                <li>{{ __('app.healthcard_important_item2') }}</li>
                <li>{{ __('app.healthcard_important_item3') }}</li>
            </ul>

            <h2 class="font-semibold text-xl text-[#0065c0] mt-4">{{ __('app.healthcard_how_title') }}</h2>
            <ol class="text-gray-600 dark:text-white space-y-2 list-decimal list-inside mt-2">
                <li>{{ __('app.healthcard_how_item1') }}</li>
                <li>{{ __('app.healthcard_how_item2') }}</li>
                <li>{{ __('app.healthcard_how_item3') }}</li>
                <li>{{ __('app.healthcard_how_item4') }}</li>
                <li>{{ __('app.healthcard_how_item5') }}</li>
                <li>{{ __('app.healthcard_how_item6') }}</li>
            </ol>

            <p class="mt-4 text-gray-600 dark:text-white">
                {!! __('app.healthcard_note') !!}
            </p>
        </div>

        <div class="flex">
            <flux:spacer />
            <flux:modal.close>
                <flux:button type="submit" variant="primary" color="cyan">
                    {{ __('app.close') }}
                </flux:button>
            </flux:modal.close>
        </div>
    </div>
</flux:modal>
