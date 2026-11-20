<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInterestsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_ids' => ['required', 'array', 'min:3', 'max:6'],
            'category_ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:categories,id,is_active,1',
            ],
        ];
    }
}
