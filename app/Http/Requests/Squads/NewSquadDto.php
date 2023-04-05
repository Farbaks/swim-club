<?php

namespace App\Http\Requests\Squads;

use Illuminate\Foundation\Http\FormRequest;

class NewSquadDto extends FormRequest
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
            'description' => 'nullable|max:50|string',
            'rank' => 'nullable|max:50|string'
        ];
    }

    protected $stopOnFirstFailure = true;
}
