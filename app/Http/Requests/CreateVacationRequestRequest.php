<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\VacationDurationMustBeWithinAvailableVacationDaysRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class CreateVacationRequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'start' => ['required', 'date', ],
            'end' => [
                'required', 'date', 'after_or_equal:start',
                new VacationDurationMustBeWithinAvailableVacationDaysRule($this->start, $this->end, auth()->user()->available_vacation_days),
            ],
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @return void
     *
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        if ($this->wantsJson()) {
            throw new ValidationException($validator, response()->json($validator->errors(), 422));
        }
        parent::failedValidation($validator);
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $rule = $validator->getRules()['end'][3];
    
            $this->merge([
                'calculated_working_days_duration' => $rule->getCalculatedWorkingDaysDuration()
            ]);
        });
    }
}
