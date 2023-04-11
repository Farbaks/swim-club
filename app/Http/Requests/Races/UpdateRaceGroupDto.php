<?php

namespace App\Http\Requests\Races;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRaceGroupDto extends FormRequest
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
            'description' => 'sometimes|nullable|string',
            'raceId' => 'sometimes|required|integer'
        ];
    }

    protected $stopOnFirstFailure = true;
}
