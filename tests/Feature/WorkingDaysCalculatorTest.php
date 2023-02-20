<?php

namespace Tests\Feature;

use Tests\BaseTestCase;
use App\Services\WorkingDaysCalculator;
use Carbon\Carbon;
use App\Models\Holiday;

class WorkingDaysCalculatorTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->start = Carbon::create(2023, 1, 2)->format('Y-m-d'); 
        $this->end = Carbon::create(2023, 1, 6)->format('Y-m-d'); 

        $this->holidaysDays = [
            ['day' => 2, 'month' => 1],
            ['day' => 3, 'month' => 1],
            ['day' => 4, 'month' => 1],
            ['day' => 5, 'month' => 1],
            ['day' => 6, 'month' => 1],
        ];

        foreach($this->holidaysDays as $holiday){
            Holiday::factory()->create($holiday);
        }
    }

    private function fuckSingleton()
    {
        WorkingDaysCalculator::destroy();
    }

    /**
     * All days selected for vacation are during holiday. Expected result is 0 days
     */
    public function test_holidays_are_included_as_non_working_days_successfully()
    {
        $this->fuckSingleton();

        $calculatedWorkingDaysDuration = WorkingDaysCalculator::getInstance()
            ->getWorkingDaysBetweenDates($this->start, $this->end);

        $this->assertTrue($calculatedWorkingDaysDuration === 0);

        $this->fuckSingleton();
    }

    /**
     * All days selected for vacation are during holiday, but holidays are ignored (usefull if feature breaks). Expected result is 0 days
     */
    public function test_holidays_can_be_ignored_by_passing_false_as_third_arg()
    {
        $this->fuckSingleton();

        $calculatedWorkingDaysDuration = WorkingDaysCalculator::getInstance()
            ->getWorkingDaysBetweenDates($this->start, $this->end, false);

        $this->assertTrue($calculatedWorkingDaysDuration === 5);

        $this->fuckSingleton();
    }

    /**
     * All days selected for vacation are not during holiday which is why they shouldn't affect number of free days. Expected result is 5 days
     */
    public function test_no_holidays_fall_on_selected_vacation_which_is_why_it_shouldnt_interfere_with_calcualtion()
    {
        $this->fuckSingleton();

        // Delete all holdays.
        Holiday::truncate();

        $calculatedWorkingDaysDuration = WorkingDaysCalculator::getInstance()
            ->getWorkingDaysBetweenDates($this->start, $this->end);

        $this->assertTrue($calculatedWorkingDaysDuration === 5);
        
        $this->fuckSingleton();
    }
}
