<?php

declare(strict_types=1);

use App\Models\Patient;
use App\Models\User;

it('returns full_name when user is missing', function () {
    $patient = new Patient([
        'full_name' => 'Walk In Patient',
    ]);

    expect($patient->full_name)->toBe('Walk In Patient');
});

it('falls back to user name when full_name is empty', function () {
    $user = new User(['name' => 'Linked User']);
    $patient = new Patient;
    $patient->setRelation('user', $user);

    expect($patient->full_name)->toBe('Linked User');
});
