<?php

namespace App\Http\Requests\Trainings;

use Illuminate\Foundation\Http\FormRequest;

class NewTrainingPerformanceDto extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'squadMemberId' => 'required|integer',
            'time' => 'required|date_format:i:s.v',
            'strokeId' => 'required|integer',
            'rank' => 'nullable|string',
            'points' => 'nullable|string',
            'trainingDate' => 'required|date_format:Y-m-d',
        ];
    }

    protected $stopOnFirstFailure = true;
}