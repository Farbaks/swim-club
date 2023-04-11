<?php

namespace App\Http\Requests\Races;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRaceDto extends FormRequest
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
            'requirements' => 'sometimes|nullable|string',
            'startDate' => 'sometimes|required|date_format:Y-m-d',
            'endDate' => 'sometimes|required|date_format:Y-m-d',
        ];
    }

    protected $stopOnFirstFailure = true;
}
