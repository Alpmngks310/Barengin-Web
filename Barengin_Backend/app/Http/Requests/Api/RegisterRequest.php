<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     * Normalize email to lowercase and remove whitespace.
     */
    protected function prepareForValidation(): void
    {
        $email = $this->input('email');

        if (is_string($email)) {
            $this->merge([
                'email' => strtolower(trim($email)),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'username' => [
                'nullable',
                'string',
                'min:3',
                'max:30',
                'alpha_dash',
                Rule::unique('users', 'username'),
            ],

            'email' => [
                'required',
                'string',
                'email:rfc',
                'max:150',
                Rule::unique('users', 'email'),
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],

            'adult_confirmed' => [
                'required',
                'accepted',
            ],

            'terms_accepted' => [
                'required',
                'accepted',
            ],

            'privacy_accepted' => [
                'required',
                'accepted',
            ],

            'device_name' => [
                'nullable',
                'string',
                'max:100',
            ],
        ];
    }
}
