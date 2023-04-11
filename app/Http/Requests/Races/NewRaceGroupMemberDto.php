<?php

namespace App\Http\Requests\Races;

use Illuminate\Foundation\Http\FormRequest;

class NewRaceGroupMemberDto extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required_without:swimmerId|max:50|string',
            'age' => 'required_without:swimmerId|integer',
            'club' => 'required_without:swimmerId|string',
            'swimmerId' => 'sometimes|required|integer',
            'raceGroupId' => 'required|integer',
        ];
    }

    protected $stopOnFirstFailure = true;
}
