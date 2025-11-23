<flux:modal name="hiv-modal" :dismissible="false" :closable="false">
    <div class="space-y-6">
        <div>
            <h2 class="font-bold text-center text-cyan-600 text-2xl">{{ __('app.hiv_service_title') }}</h2>
        </div>

        <div>
            <h2 class="font-semibold text-xl">{{ __('app.hiv_what_title') }}</h2>
            <p class="text-gray-600 dark:text-white">
                {{ __('app.hiv_what_description') }}
            </p>

            <h2 class="font-semibold text-xl text-[#0065c0] mt-4">{{ __('app.hiv_who_title') }}</h2>
            <ul class="text-gray-600 dark:text-white space-y-2 list-disc list-inside mt-2">
                <li>{!! __('app.hiv_who_item1') !!}</li>
                <li>{!! __('app.hiv_who_item2') !!}</li>
                <li>{!! __('app.hiv_who_item3') !!}</li>
            </ul>

            <h2 class="font-semibold text-xl text-[#0065c0] mt-4">{{ __('app.hiv_offer_title') }}</h2>
            <ul class="text-gray-600 dark:text-white space-y-2 list-disc list-inside mt-2">
                <li>{!! __('app.hiv_offer_item1') !!}</li>
                <li>{!! __('app.hiv_offer_item2') !!}</li>
                <li>{!! __('app.hiv_offer_item3') !!}</li>
                <li>{!! __('app.hiv_offer_item4') !!}</li>
                <li>{!! __('app.hiv_offer_item5') !!}</li>
            </ul>

            <h2 class="font-semibold text-xl text-[#0065c0] mt-4">{{ __('app.hiv_important_title') }}</h2>
            <ul class="text-gray-600 dark:text-white space-y-2 list-disc list-inside mt-2">
                <li>{{ __('app.hiv_important_item1') }}</li>
                <li>{{ __('app.hiv_important_item2') }}</li>
                <li>{{ __('app.hiv_important_item3') }}</li>
            </ul>

            <h2 class="font-semibold text-xl text-[#0065c0] mt-4">{{ __('app.hiv_access_title') }}</h2>
            <ol class="text-gray-600 dark:text-white space-y-2 list-decimal list-inside mt-2">
                <li>{{ __('app.hiv_access_item1') }}</li>
                <li>{{ __('app.hiv_access_item2') }}</li>
                <li>{{ __('app.hiv_access_item3') }}</li>
                <li>{{ __('app.hiv_access_item4') }}</li>
            </ol>

            <p class="mt-4 text-gray-600 dark:text-white">
                {!! __('app.hiv_remember') !!}
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
