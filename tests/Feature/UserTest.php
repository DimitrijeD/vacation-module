<?php

namespace Tests\Feature;

use Tests\BaseTestCase;
use App\Models\User;
use App\Models\VacationRequest;

class UserTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->withHeader('Authorization', "Bearer {$this->user->createToken('app')->plainTextToken}");

        $this->endpoint = '/api/user';
    }

    public function test_get_user()
    {
        $response = $this->get($this->endpoint);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id', 'name', 'email', 'role', 'available_vacation_days', 'updated_at', 'created_at',  
            ]);
    }

  
}
