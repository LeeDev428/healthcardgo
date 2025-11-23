<?php

declare(strict_types=1);

use function Pest\Laravel\get;

it('loads the patient registration page', function () {
    $response = get(route('register.patient'));

    $response->assertSuccessful();
    $response->assertSee('Patient Registration');
    $response->assertSee('Personal Information');
    $response->assertSee('Account Security');
});
