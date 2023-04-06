<?php

namespace App\Http\Requests\Relationships;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRelationshipDto extends FormRequest
{
     /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'status' => 'required|in:active,inactive',
        ];
    }

    protected $stopOnFirstFailure = true;
}
