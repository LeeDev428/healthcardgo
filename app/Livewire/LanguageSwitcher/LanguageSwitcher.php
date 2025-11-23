<?php

namespace App\Livewire\LanguageSwitcher;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class LanguageSwitcher extends Component
{
    public string $locale;

    // public array $availableLocales = [
    //     'en' => 'English',
    //     'fil' => 'Tagalog',
    //     'ceb' => 'Bisaya',
    // ];

    public function mount()
    {
        $this->locale = Session::get('locale', config('app.locale'));
    }

    // public function switchLanguage($lang)
    // {
    //     if (array_key_exists($lang, $this->availableLocales)) {
    //         Session::put('locale', $lang);
    //         App::setLocale($lang);
    //     }

    //     return redirect(request()->header('Referer') ?? '/');
    // }

    public function updatedLocale($locale)
    {
        Session::put('locale', $locale);
        App::setLocale($locale);

        return redirect(request()->header('Referer') ?? '/');
    }

    public function render()
    {
        return view('livewire.language-switcher.language-switcher');
    }
}
