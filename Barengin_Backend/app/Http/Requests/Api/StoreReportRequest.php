<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reported_user_id' => [
                'nullable',
                'integer',
                'exists:users,id',
                'required_without:activity_id',
                'prohibited_with:activity_id',
            ],
            'activity_id' => [
                'nullable',
                'integer',
                'exists:activities,id',
                'required_without:reported_user_id',
                'prohibited_with:reported_user_id',
            ],
            'reason' => [
                'required',
                Rule::in([
                    'inappropriate_behavior',
                    'misleading_activity',
                    'harassment_or_threat',
                    'spam_or_scam',
                    'other',
                ]),
            ],
            'details' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
