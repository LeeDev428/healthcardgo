<?php

namespace Tests;

use App\Models\Patient;
use App\Models\User;
use App\Services\HealthCardService;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected Patient $patient;

    protected HealthCardService $healthCardService;

    protected User $user;
}
