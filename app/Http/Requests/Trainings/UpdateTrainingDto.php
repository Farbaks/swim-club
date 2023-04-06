<?php

namespace App\Http\Requests\Trainings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTrainingDto extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|max:50|string',
            'description' => 'sometimes|required|string',
            'requirements' => 'sometimes|required|string',
            'startTime' => 'sometimes|required|date_format:H:i',
            'endTime' => 'sometimes|required|date_format:H:i',
            'day' => 'sometimes|required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'interval' => 'sometimes|required|in:once,weekly',
            'squadId' => 'sometimes|required|integer',
        ];
    }

    protected $stopOnFirstFailure = true;
}
