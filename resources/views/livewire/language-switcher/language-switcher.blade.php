<flux:select wire:model.live="locale" name="language" id="language-select" class="max-w-fit">
  <flux:select.option value="en">
    <div class="flex items-center gap-2">
        <img src="{{ asset('assets/images/united.png') }}" alt="English" >
        English
    </div>
  </flux:select.option>
  <flux:select.option value="fil">
    Tagalog
  </flux:select.option>
  <flux:select.option value="ceb">
    Bisaya
  </flux:select.option>
</flux:select>
{{-- <flux:dropdown position="bottom" align="end">
    <flux:menu>
        @foreach ($availableLocales as $code => $name)
            <flux:menu.item wire:click="switchLanguage('{{ $code }}')">
                {{ $name }}
            </flux:menu.item>
        @endforeach
    </flux:menu>
</flux:dropdown> --}}
