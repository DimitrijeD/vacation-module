<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;
use App\Models\VacationRequest;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vacation>
 */
class VacationRequestFactory extends Factory
{
    public $start = '';
    public $end = '';
    public $working_days_duration = 0;

    public function setDurationAndIntervals()
    {
        $this->start = Carbon::create(2023, 2, 2);
        $this->end = Carbon::create(2023, 2, 10);

        $this->working_days_duration = \App\Services\WorkingDaysCalculator::getInstance()
            ->getWorkingDaysBetweenDates($this->start, $this->end);;
    }
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $this->setDurationAndIntervals();
        
        return [
            'user_id' => \App\Models\User::factory(),
            'start' => $this->start , 
            'end' => $this->end,
            'status' => VacationRequest::STATUS_PENDING,
            'working_days_duration' => $this->working_days_duration,
        ];
    }
}
