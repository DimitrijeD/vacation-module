<?php

namespace Tests\Feature;

use Tests\BaseTestCase;
use App\Models\User;
use App\Models\VacationRequest;

class ManagerApproveVacationRequestTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->vacation = VacationRequest::factory()->create();
        $this->user = $this->vacation->user;
        $this->manager = User::factory(['role' => User::ROLE_MANAGER])->create();

        $this->payload = [
            'id' => $this->vacation->id, 
        ]; 

        $this->withHeader('Authorization', "Bearer {$this->manager->createToken('app')->plainTextToken}");

        $this->endpoint = '/api/vacations/approve';
    }

    public function test_manager_approves_vacation_request()
    {
        $response = $this->post($this->endpoint, $this->payload);

        $response->assertStatus(200)->assertJson([
            'message' => 'Record updated successfully',
        ]);

        $this->assertDatabaseHas((new VacationRequest())->getTable(), [
            'id' => $this->payload['id'],
            'status' => VacationRequest::STATUS_APPROVED,
        ]);
    }

    public function test_approving_vacation_request_changes_number_of__available_vacation_days__user_has()
    {
        $willHaveAvailVacationDaysAfterChange = $this->user->available_vacation_days - $this->vacation->working_days_duration;

        $this->post($this->endpoint, $this->payload);

        $this->assertDatabaseHas((new User())->getTable(), [
            'id' => $this->user->id,
            'available_vacation_days' => $willHaveAvailVacationDaysAfterChange,
        ]);
    }
}
