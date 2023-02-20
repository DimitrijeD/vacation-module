<?php

namespace Tests\Feature;

use Tests\BaseTestCase;
use App\Models\User;
use App\Models\VacationRequest;

class ManagerRejectsVacationRequestTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->vacation = VacationRequest::factory()->create();
        $this->manager = User::factory(['role' => User::ROLE_MANAGER])->create();

        $this->payload = [
            'id' => $this->vacation->id, 
        ]; 

        $this->withHeader('Authorization', "Bearer {$this->manager->createToken('app')->plainTextToken}");

        $this->endpoint = '/api/vacations/reject';
    }

    public function test_manager_rejects_vacation_request()
    {
        $response = $this->post($this->endpoint, $this->payload);

        $response->assertStatus(200)->assertJson([
            'message' => 'Record updated successfully',
        ]);

        $this->assertDatabaseHas((new VacationRequest())->getTable(), [
            'id' => $this->payload['id'],
            'status' => VacationRequest::STATUS_REJECTED,
        ]);

    }
}
