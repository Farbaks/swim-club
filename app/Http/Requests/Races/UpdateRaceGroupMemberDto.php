<?php

namespace App\Http\Requests\Races;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRaceGroupMemberDto extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'time' => 'sometimes|required|date_format:i:s.v',
            'strokeId' => 'sometimes|required|integer',
            'rank' => 'nullable|string',
            'points' => 'sometimes|required|integer',
        ];
    }

    protected $stopOnFirstFailure = true;
}