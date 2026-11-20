<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['sometimes', 'required', 'integer', 'exists:categories,id,is_active,1'],
            'title' => ['sometimes', 'required', 'string', 'max:120'],
            'description' => ['sometimes', 'required', 'string', 'min:10'],
            'participant_level' => ['sometimes', 'required', Rule::in(['beginner', 'all_levels', 'experienced'])],
            'starts_at' => ['sometimes', 'required', 'date', 'after:now'],
            'ends_at' => ['sometimes', 'required', 'date'],
            'timezone' => ['sometimes', 'required', 'timezone'],
            'venue_name' => ['sometimes', 'required', 'string', 'max:150'],
            'address' => ['sometimes', 'required', 'string', 'max:255'],
            'area' => ['sometimes', 'nullable', 'string', 'max:120'],
            'city' => ['sometimes', 'required', 'string', 'max:100'],
            'province' => ['sometimes', 'nullable', 'string', 'max:100'],
            'country_code' => ['sometimes', 'required', 'string', 'size:2'],
            'latitude' => ['sometimes', 'nullable', 'numeric', 'between:-90,90', 'required_with:longitude'],
            'longitude' => ['sometimes', 'nullable', 'numeric', 'between:-180,180', 'required_with:latitude'],
            'capacity' => ['sometimes', 'required', 'integer', 'between:2,15'],
            'budget_amount' => ['sometimes', 'required', 'integer', 'min:0', 'max:100000000'],
            'currency' => ['sometimes', 'required', 'string', 'size:3'],
            'cover_image_path' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }
}
