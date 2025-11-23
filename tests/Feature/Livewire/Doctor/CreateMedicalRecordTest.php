<?php

use App\Livewire\Doctor\CreateMedicalRecord;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(CreateMedicalRecord::class)
        ->assertStatus(200);
});
