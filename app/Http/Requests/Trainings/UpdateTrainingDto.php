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
            'name' => 'nullable|max:50|string',
            'description' => 'nullable|string',
            'requirements' => 'nullable|string',
            'startTime' => 'nullable|date_format:H:i',
            'endTime' => 'nullable|date_format:H:i',
            'day' => 'nullable|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'interval' => 'nullable|in:once,weekly',
            'squadId' => 'nullable|integer',
        ];
    }

    protected $stopOnFirstFailure = true;
}
