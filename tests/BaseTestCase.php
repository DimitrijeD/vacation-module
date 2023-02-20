<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;

class BaseTestCase extends TestCase
{
    // makes every test refresh database
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // seeds Holidays after refreshing database
        $this->seed(\Database\Seeders\HolidaySeeder::class);
    }
}