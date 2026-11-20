<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', 'exists:categories,id,is_active,1'],
            'title' => ['required', 'string', 'max:120'],
            'description' => ['required', 'string', 'min:10'],
            'participant_level' => ['required', Rule::in(['beginner', 'all_levels', 'experienced'])],
            'starts_at' => ['required', 'date', 'after:now'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'timezone' => ['sometimes', 'required', 'timezone'],
            'venue_name' => ['required', 'string', 'max:150'],
            'address' => ['required', 'string', 'max:255'],
            'area' => ['nullable', 'string', 'max:120'],
            'city' => ['required', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'country_code' => ['sometimes', 'required', 'string', 'size:2'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90', 'required_with:longitude'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180', 'required_with:latitude'],
            'capacity' => ['required', 'integer', 'between:2,15'],
            'budget_amount' => ['sometimes', 'required', 'integer', 'min:0', 'max:100000000'],
            'currency' => ['sometimes', 'required', 'string', 'size:3'],
            'cover_image_path' => ['nullable', 'string', 'max:255'],
        ];
    }
}
