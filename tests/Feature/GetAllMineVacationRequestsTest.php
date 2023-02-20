<?php

namespace Tests\Feature;

use Tests\BaseTestCase;
use App\Models\User;
use App\Models\VacationRequest;

class GetAllMineVacationRequestsTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->withHeader('Authorization', "Bearer {$this->user->createToken('app')->plainTextToken}");

        $this->endpoint = '/api/vacations/all';
    }

    public function test_gets_only_pending_vacation_request()
    {
        $approved = VacationRequest::factory(3)->create([
            'user_id' => $this->user->id,
            'status' => VacationRequest::STATUS_APPROVED
        ]);

        $pending = VacationRequest::factory(3)->create([
            'user_id' => $this->user->id,
            'status' => VacationRequest::STATUS_PENDING
        ]);

        $rejected = VacationRequest::factory(3)->create([
            'user_id' => $this->user->id,
            'status' => VacationRequest::STATUS_REJECTED
        ]);
        
        $response = $this->get($this->endpoint);

        $response->assertStatus(200)->assertJsonCount(count($approved->merge($pending)->merge($rejected)));
    }

    public function test_gets_nothing_if_pending_doesnt_exist()
    {
        $response = $this->get($this->endpoint);

        $response->assertStatus(200)->assertJson([]);
    }

}
