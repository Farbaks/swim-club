<?php

namespace App\Http\Requests\Trainings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTrainingPerformanceDto extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'squadMemberId' => 'nullable|integer',
            'time' => 'nullable|date_format:i:s.v',
            'strokeId' => 'nullable|integer',
            'rank' => 'nullable|string',
            'points' => 'nullable|string',
            'trainingDate' => 'nullable|date_format:Y-m-d',
        ];
    }

    protected $stopOnFirstFailure = true;
}