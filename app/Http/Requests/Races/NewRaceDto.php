<?php

namespace App\Http\Requests\Races;

use Illuminate\Foundation\Http\FormRequest;

class NewRaceDto extends FormRequest
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
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'startDate' => 'required|date_format:Y-m-d|after:yesterday',
            'endDate' => 'required|date_format:Y-m-d|after_or_equal:startDate',
        ];
    }

    protected $stopOnFirstFailure = true;
}
