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
            'squadMemberId' => 'sometimes|required|integer',
            'time' => 'sometimes|required|date_format:i:s.v',
            'strokeId' => 'sometimes|required|integer',
            'rank' => 'sometimes|required|string',
            'points' => 'sometimes|required|string',
            'trainingDate' => 'sometimes|required|date_format:Y-m-d',
        ];
    }

    protected $stopOnFirstFailure = true;
}