<?php

namespace Tests\Feature;

use Tests\BaseTestCase;
use App\Models\User;
use App\Models\VacationRequest;

class DeletePendingVacationRequestTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->vacation = VacationRequest::factory()->create([
            'user_id' => $this->user->id,
        ]);
        $this->withHeader('Authorization', "Bearer {$this->user->createToken('app')->plainTextToken}");

        $this->endpoint = '/api/vacations/delete-pending';
    }

    public function test_user_deletes_pending_vacation_request()
    {
        $response = $this->delete($this->endpoint);

        $response->assertStatus(200)->assertJson([
            'message' => 'Record deleted successfully',
        ]);
    }
}
