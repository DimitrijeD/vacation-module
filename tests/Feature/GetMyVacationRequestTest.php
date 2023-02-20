<?php

namespace Tests\Feature;

use Tests\BaseTestCase;
use App\Models\User;
use App\Models\VacationRequest;

class GetMyVacationRequestTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->withHeader('Authorization', "Bearer {$this->user->createToken('app')->plainTextToken}");

        $this->endpoint = '/api/vacations/get-pending';
    }

    public function test_gets_only_pending_vacation_request()
    {
        $this->vacation = VacationRequest::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->get($this->endpoint);

        $response->assertStatus(200)->assertJsonStructure([
            "id", "user_id", "start", "end", "status", "created_at", "updated_at",
        ]);
    }

    public function test_gets_nothing_if_pending_doesnt_exist()
    {
        $response = $this->get($this->endpoint);

        $response->assertStatus(200)->assertJson([]);
    }

}
