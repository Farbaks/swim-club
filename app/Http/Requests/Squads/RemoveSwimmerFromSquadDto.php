<?php

namespace App\Http\Requests\Squads;

use Illuminate\Foundation\Http\FormRequest;

class RemoveSwimmerFromSquadDto extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'swimmer' => 'required|integer',
        ];
    }

    protected $stopOnFirstFailure = true;
}
