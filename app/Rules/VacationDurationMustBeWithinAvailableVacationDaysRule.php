<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class VacationDurationMustBeWithinAvailableVacationDaysRule implements Rule
{
    private $message = '';
    public $calculatedWorkingDaysDuration = 0;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($start, $end, $available_vacation_days)
    {
        $this->start = $start;  
        $this->end = $end;  
        $this->available_vacation_days = $available_vacation_days; 

    }

    /**
     * If dates provided in request, satisfy following cirtearia, request will pass:
     *  1) User must have enough vacation days available in order to submit request;
     *  2) User must have had selected more then 0 working days for the following period (time between $start and $end) 
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->calculatedWorkingDaysDuration = \App\Services\WorkingDaysCalculator::getInstance()
            ->getWorkingDaysBetweenDates($this->start, $this->end);
        
        if($this->calculatedWorkingDaysDuration == 0){
            $this->message = 'You have selected only non-working days for this vacation. Please select at least one working day to proceed with this request.';
            return false;
        }

        if($this->calculatedWorkingDaysDuration >= $this->available_vacation_days){
            $this->message = "You have selected {$this->calculatedWorkingDaysDuration} vacation days while having only {$this->available_vacation_days} available vacation days. Please select at maximum the same number of vacation days you have available.";
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        if($this->message){
            return $this->message;
        }

        return 'The validation error message.';
    }

    public function getCalculatedWorkingDaysDuration()
    {
        return $this->calculatedWorkingDaysDuration;
    }
}
