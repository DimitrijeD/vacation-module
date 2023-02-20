<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\AvailableRolesRule;

class RegisterRequest extends FormRequest
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
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'string', 'min:3', 'max:255', 'email', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'max:255'],
            'role' => ['required', 'string', new AvailableRolesRule($this->role), ]
        ];
    }
}
