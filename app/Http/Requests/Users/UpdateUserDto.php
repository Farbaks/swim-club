<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserDto extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'firstName' => 'sometimes|required|max:50|string',
            'lastName' => 'sometimes|required|max:50|string',
            'phoneNumber' => 'sometimes|required',
            'emailAddress' => 'sometimes|required|email',
            'pictureUrl' => 'sometimes|required|url',
            'gender' => 'sometimes|required|in:male,female,others',
            'address' => 'sometimes|required|string',
            'postcode' => 'sometimes|required|string',
        ];
    }

    protected $stopOnFirstFailure = true;
}
