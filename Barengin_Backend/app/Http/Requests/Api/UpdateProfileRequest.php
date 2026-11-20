<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'username' => ['sometimes', 'nullable', 'string', 'min:3', 'max:30', 'alpha_dash', Rule::unique('users', 'username')->ignore($userId)],
            'display_name' => ['sometimes', 'required', 'string', 'max:80'],
            'bio' => ['sometimes', 'nullable', 'string', 'max:500'],
            'area' => ['sometimes', 'nullable', 'string', 'max:120'],
            'city' => ['sometimes', 'nullable', 'string', 'max:100'],
            'province' => ['sometimes', 'nullable', 'string', 'max:100'],
            'country_code' => ['sometimes', 'required', 'string', 'size:2'],
            'avatar' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }
}
