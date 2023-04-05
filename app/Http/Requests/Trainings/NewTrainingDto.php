<?php

namespace App\Http\Requests\Trainings;

use Illuminate\Foundation\Http\FormRequest;

class NewTrainingDto extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|max:50|string',
            'description' => 'nullable|string',
            'requirements' => 'nullable|string',
            'startTime' => 'required|date_format:H:i',
            'endTime' => 'required|date_format:H:i',
            'day' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'interval' => 'required|in:once,weekly',
            'squadId' => 'required|integer',
        ];
    }

    protected $stopOnFirstFailure = true;
}