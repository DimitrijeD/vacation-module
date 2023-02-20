<?php

namespace Tests\Feature;

use Tests\BaseTestCase;
use App\Models\User;
use App\Models\VacationRequest;
use Carbon\Carbon;

class UpdateVacationRequestTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        // As long as ($this->oldEndDate <= $this->newEndDate) tests should pass
        $this->oldEndDate = Carbon::create(2023, 2, 10);
        $this->newEndDate = $this->oldEndDate->addWeek();

        $this->vacation = VacationRequest::factory()->create([
            'user_id' => $this->user->id,
            'status' => VacationRequest::STATUS_PENDING,
            'start' => Carbon::create(2023, 2, 1)->format('Y-m-d'), 
            'end' => $this->oldEndDate->format('Y-m-d'), 
        ]);

        $this->payload = [
            'id' => $this->vacation->id,
            'start' => $this->vacation->start,
            'end' => $this->newEndDate->format('Y-m-d'), 
        ]; 
        
        $this->withHeader('Authorization', "Bearer {$this->user->createToken('app')->plainTextToken}");

        $this->endpoint = '/api/vacations/update';
    }

    public function test_sending_only_id()
    {
        $this->post($this->endpoint, [
            'id' => $this->payload['id'],
        ]);
        
        $this->assertDatabaseHas((new VacationRequest())->getTable(), [
            'user_id' => $this->user->id,
            'start' => $this->payload['start'],
            'end' => $this->payload['end'],
            'status' => VacationRequest::STATUS_PENDING,
        ]);
    }

    public function test_vacation_request_can_be_updated_without_submitting_end_date()
    {
        $this->post($this->endpoint, [
            'id' => $this->payload['id'],
            'start' => $this->payload['start'],
        ]);

        $this->assertDatabaseHas((new VacationRequest())->getTable(), [
            'user_id' => $this->user->id,
            'start' => $this->payload['start'],
            'end' => $this->payload['end'],
            'status' => VacationRequest::STATUS_PENDING,
        ]);
    }

    public function test_vacation_request_can_be_updated_without_submitting_start_date()
    {
        $this->post($this->endpoint, [
            'id' => $this->payload['id'],
            'end' => $this->payload['end'],
        ]);

        $this->assertDatabaseHas((new VacationRequest())->getTable(), [
            'user_id' => $this->user->id,
            'start' => $this->payload['start'],
            'end' => $this->payload['end'],
            'status' => VacationRequest::STATUS_PENDING,
        ]);
    }

    public function test_updated_vacation_request_json_structure()
    {
        $response = $this->post($this->endpoint, $this->payload);
        
        $response->assertStatus(200)->assertJsonStructure([
            'user_id', 'start', 'end', 'working_days_duration', 'status', 'updated_at', 'created_at', 'id', 
        ]);
    }

    public function test_changes_value_for__working_days_duration__property_on_dates_change()
    {
        $this->post($this->endpoint, $this->payload);

        $oldDuration = $this->vacation->working_days_duration;
        $newDuration = VacationRequest::find($this->vacation->id)->working_days_duration;
        
        $this->assertTrue(
            $oldDuration != $newDuration
        );
    }

}
