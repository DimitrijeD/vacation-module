<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\VacationRequest;
use App\Rules\VacationDurationMustBeWithinAvailableVacationDaysRule;

class UpdateVacationRequestRequest extends FormRequest
{
    public $vacationRequest = null;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->vacationRequest = VacationRequest::where([
            'id' => $this->id, // Use both in query to make sure only owner can update model. 
            'user_id' => auth()->user()->id, 
        ])->first();
        
        return $this->vacationRequest 
            ? true 
            : false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $this->start = $this->start ? $this->start : $this->vacationRequest->start;
        $this->end = $this->end ? $this->end : $this->vacationRequest->end;
        
        return [
            'id' => ['required', 'integer', ],
            'start' => ['required', 'date', ],
            'end' => [ 
                new VacationDurationMustBeWithinAvailableVacationDaysRule(
                    $this->start, 
                    $this->end, 
                    auth()->user()->available_vacation_days
                ),
                'required', 'date', 'after_or_equal:start', 
            ],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $rule = $validator->getRules()['end'][0];
    
            $this->merge([
                'calculated_working_days_duration' => $rule->getCalculatedWorkingDaysDuration(),
                'vacationRequest' => $this->vacationRequest,
                'start' => $this->start,
                'end' => $this->end,
            ]);
        });
    }
}
