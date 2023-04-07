<?php

namespace App\Http\Requests\Squads;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSquadDto extends FormRequest
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
            'rank' => 'nullable|max:50|string'
        ];
    }

    protected $stopOnFirstFailure = true;
}
