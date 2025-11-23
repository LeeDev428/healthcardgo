<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
  @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:header container class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700" style="z-index: 30 !important;">
        <flux:spacer />
        <livewire:notifications.notification-bell />
    </flux:header>

  <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900" style="z-index: 40 !important;">
    <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

    <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
      <x-app-logo />
    </a>

    <flux:navlist variant="outline">
      <flux:navlist.group :heading="__('Platform')" class="grid">
        @if (auth()->user()->hasAnyRole(['super_admin']))
          <flux:navlist.item icon="home" :href="route('admin.dashboard')"
            :current="request()->routeIs('admin.dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
          {{-- <flux:navlist.item icon="user-plus" :href="route('admin.approvals')" :current="request()->routeIs('admin.approvals')" wire:navigate>Pending Approvals</flux:navlist.item> --}}
          <flux:navlist.item icon="calendar-days" :href="route('admin.appointments')"
            :current="request()->routeIs('admin.appointments')" wire:navigate>Appointments</flux:navlist.item>
          <flux:navlist.item icon="users" :href="route('admin.patients')"
            :current="request()->routeIs('admin.patients')" wire:navigate>Patients</flux:navlist.item>
            <flux:navlist.item icon="identification" :href="route('admin.health-cards')"
            :current="request()->routeIs('admin.health-cards')" wire:navigate>Health Cards</flux:navlist.item>
            <flux:navlist.item icon="exclamation-triangle" :href="route('admin.disease-surveillance')" :current="request()->routeIs('admin.disease-surveillance')" wire:navigate>Disease Surveillance</flux:navlist.item>
            <flux:navlist.item icon="document-text" :href="route('admin.reports')"
            :current="request()->routeIs('admin.reports')" wire:navigate>Reports</flux:navlist.item>
            {{-- <flux:navlist.item icon="presentation-chart-line" wire:navigate>Forecasting</flux:navlist.item> --}}
            <flux:navlist.item icon="heart" :href="route('admin.services')"
              :current="request()->routeIs('admin.services')" wire:navigate>Services</flux:navlist.item>
          <flux:navlist.item icon="map-pin" :href="route('admin.barangays')"
            :current="request()->routeIs('admin.barangays')" wire:navigate>Barangays</flux:navlist.item>
          <flux:navlist.item icon="user-group" :href="route('admin.users')"
            :current="request()->routeIs('admin.users')" wire:navigate>Users</flux:navlist.item>
          <flux:navlist.item icon="megaphone" :href="route('admin.announcements')"
            :current="request()->routeIs('admin.announcements')" wire:navigate>Announcements</flux:navlist.item>
        @endif

        @if (auth()->user()->hasAnyRole(['healthcare_admin']))
          <flux:navlist.item icon="home" :href="route('healthcare_admin.dashboard')" :current="request()->routeIs('healthcare_admin.dashboard')"
            wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
          <flux:navlist.item icon="calendar-days" :href="route('healthcare_admin.appointments')"
            :current="request()->routeIs('healthcare_admin.appointments')" wire:navigate>Appointments
          </flux:navlist.item>
          <flux:navlist.item icon="user" :href="route('healthcare_admin.patients')"
            :current="request()->routeIs('healthcare_admin.patients')" wire:navigate>Patients</flux:navlist.item>
          <flux:navlist.item icon="document-text" :href="route('healthcare_admin.reports')"
            :current="request()->routeIs('healthcare_admin.reports')" wire:navigate>Reports</flux:navlist.item>
          <flux:navlist.item icon="megaphone" :href="route('healthcare_admin.announcements')"
            :current="request()->routeIs('healthcare_admin.announcements')" wire:navigate>Announcements</flux:navlist.item>
        @endif

        {{-- @if (auth()->user()->hasAnyRole(['patient']))
          <flux:navlist.item icon="calendar-days" :href="route('patient.appointments.list')"
            :current="request()->routeIs('patient.appointments.*')" wire:navigate>My Appointments</flux:navlist.item>
          <flux:navlist.item icon="calendar" :href="route('patient.book-appointment')"
            :current="request()->routeIs('patient.book-appointment')" wire:navigate>Book Appointment
          </flux:navlist.item>
          <flux:navlist.item icon="identification" :href="route('patient.health-card')"
            :current="request()->routeIs('patient.health-card')" wire:navigate>My Health Card</flux:navlist.item>
        @endif --}}

        {{-- <flux:navlist.item icon="bell-alert" wire:navigate>Notifications</flux:navlist.item> --}}
      </flux:navlist.group>
    </flux:navlist>

    <flux:spacer />

    <!-- Desktop User Menu -->
    <flux:dropdown class="hidden lg:block" position="bottom" align="start">
      <flux:profile :name="auth()->user()->name" :initials="auth()->user()->initials()"
        icon:trailing="chevrons-up-down" />

      <flux:menu class="w-[220px]">
        <flux:menu.radio.group>
          <div class="p-0 text-sm font-normal">
            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
              <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                <span
                  class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                  {{ auth()->user()->initials() }}
                </span>
              </span>

              <div class="grid flex-1 text-start text-sm leading-tight">
                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
              </div>
            </div>
          </div>
        </flux:menu.radio.group>

        <flux:menu.separator />

        <flux:menu.radio.group>
          <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}
          </flux:menu.item>
        </flux:menu.radio.group>

        <flux:menu.separator />

        <form method="POST" action="{{ route('logout') }}" class="w-full">
          @csrf
          <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
            {{ __('Log Out') }}
          </flux:menu.item>
        </form>
      </flux:menu>
    </flux:dropdown>
  </flux:sidebar>

  <!-- Mobile User Menu -->
  <flux:header class="lg:hidden">
    <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

    <flux:spacer />

    <flux:dropdown position="top" align="end">
      <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

      <flux:menu>
        <flux:menu.radio.group>
          <div class="p-0 text-sm font-normal">
            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
              <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                <span
                  class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                  {{ auth()->user()->initials() }}
                </span>
              </span>

              <div class="grid flex-1 text-start text-sm leading-tight">
                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
              </div>
            </div>
          </div>
        </flux:menu.radio.group>

        <flux:menu.separator />

        <flux:menu.radio.group>
          <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}
          </flux:menu.item>
        </flux:menu.radio.group>

        <flux:menu.separator />

        <form method="POST" action="{{ route('logout') }}" class="w-full">
          @csrf
          <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
            {{ __('Log Out') }}
          </flux:menu.item>
        </form>
      </flux:menu>
    </flux:dropdown>
  </flux:header>

  {{ $slot }}

  @fluxScripts
</body>

</html>
