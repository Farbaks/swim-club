<?php

namespace App\Http\Requests\Races;

use Illuminate\Foundation\Http\FormRequest;

class NewRaceGroupDto extends FormRequest
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
            'raceId' => 'required|integer'
        ];
    }

    protected $stopOnFirstFailure = true;
}
