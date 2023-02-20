<?php

namespace Tests\Feature;

use Tests\BaseTestCase;
use App\Models\User;
use App\Models\VacationRequest;
use Carbon\Carbon;

class StoreVacationRequestTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->payload = [
            'start' => Carbon::create(2023, 2, 1)->format('Y-m-d'), 
            'end' => Carbon::create(2023, 2, 5)->format('Y-m-d'), 
        ]; 

        $this->withHeader('Authorization', "Bearer {$this->user->createToken('app')->plainTextToken}");

        $this->endpoint = '/api/vacations/store';
    }

    public function test_user_creates_new_request_to_get_vacation()
    {
        $response = $this->post($this->endpoint, $this->payload);
        
        $response->assertStatus(201)->assertJsonStructure([
            'user_id', 'start', 'end', 'status', 'updated_at', 'created_at', 'id', 
        ]);

        $this->assertDatabaseHas((new VacationRequest())->getTable(), [
            'user_id' => $this->user->id,
            'start' => $this->payload['start'],
            'end' => $this->payload['end'],
            'status' => VacationRequest::STATUS_PENDING,
        ]);

        // dd($response->json());
    }

    public function test_user_cannot_create_multiple_vacation_requests()
    {
        VacationRequest::factory(['user_id' => $this->user->id])->create();
        
        $response = $this->post($this->endpoint, $this->payload);
        
        $response->assertJson([
            'message' => "you already have pending vacation request. Please wait until it is resolved before submitting another request."
        ]);
    }
}
