@props([
    'id' => '',
])

<header>
    <nav class="section__container nav__container">
        <div class="nav__logo">Health<span>Card</span><span>Go</span></div>
        <ul class="nav__links">
            <li class="link">
                <a href="{{ route('home') }}" data-key="nav_home">{{ __('app.nav_home') }}</a>
            </li>
            <li class="link">
                <a href="/#about" data-key="nav_about_us">{{ __('app.nav_about') }}</a>
            </li>
            <li class="link">
                <a href="/#service" data-key="nav_services">{{ __('app.nav_services') }}</a>
            </li>
            <li class="link">
                <a href="/#pages" data-key="nav_heat_map">{{ __('app.nav_heat_map') }}</a>
            </li>
            <li class="link">
                <a href="/#blog" data-key="nav_blog">{{ __('app.nav_blog') }}</a>
            </li>
            <li class="link">
                <a href="{{ route('announcements') }}" data-key="nav_announce">{{ __('app.nav_announcements') }}</a>
            </li>
        </ul>
        {{-- <div class="dropdown">
            <button class="btn" id="current-lang-btn">
                <img src="{{ asset('assets/images/united.png') }}" alt="">
                <span id="current-lang-text">English</span>
            </button>
            <div class="dropdown-content">
                <button class='lang-button' data-lang="tagalog">
                    <img src="{{ asset('assets/images/flag.png') }}" alt="">Tagalog</button>
                <button class='lang-button' data-lang="english">
                    <img src="{{ asset('assets/images/united.png') }}" alt="">English</button>
                <button class='lang-button' data-lang="bisaya">
                    <img src="{{ asset('assets/images/flag.png') }}" alt="">Bisaya</button>
            </div>
        </div> --}}
        <div class="flex gap-2">
            <livewire:language-switcher.language-switcher />

            {{-- <a href="{{ route('login') }}" class="nav__profile-link" style="text-decoration: none;">
                <div class="nav__profile" id="">
                    <img src="{{ asset('assets/images/user.png') }}" alt="Profile Logo" class="profile-logo" />
                </div>
            </a> --}}
            @if (Auth::check() && Auth::user()->hasAnyRole(['patient']))
            <flux:dropdown position="bottom" align="end">
                <flux:profile avatar="{{ asset('assets/images/user.png') }}" />

                <flux:navmenu>
                    <flux:navmenu.item :href="route('patient.profile')" icon="user-circle">
                        Profile
                    </flux:navmenu.item>
                    <flux:navmenu.item :href="route('patient.book-appointment')" icon="calendar-days">
                        Book Appointment
                    </flux:navmenu.item>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full cursor-pointer">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:navmenu>
            </flux:dropdown>
            @elseif (Auth::check() && Auth::user()->hasAnyRole(['super_admin', 'healthcare_admin']))
            <flux:button href="{{ route('dashboard') }}" variant="primary" color="orange">
                {{ __('app.dashboard') }}
            </flux:button>
            @else
            {{-- <a href="{{ route('login') }}" class="nav__profile-link" style="text-decoration: none;">
                <div class="nav__profile" id="">
              bu <img src="{{ asset('assets/images/user.png') }}" alt="Profile Logo" class="profile-logo" />
                </div>
            </a> --}}
            <flux:button href="{{ route('login') }}" variant="primary" color="orange">
                Login
            </flux:button>
            @endif
        </div>
    </nav>
    <div class="section__container header__container" id="{{ $id }}">
        {{ $slot }}
    </div>
</header>

