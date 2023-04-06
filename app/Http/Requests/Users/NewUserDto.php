<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class NewUserDto extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'firstName' => 'required|max:50|string',
            'lastName' => 'required|max:50|string',
            'phoneNumber' => 'required',
            'emailAddress' => 'required|email',
            'password' => 'required|min:8|string',
            'dateOfBirth' => 'required|date_format:Y-m-d|before:today',
            'pictureUrl' => 'sometimes|required|url',
            'role' => 'required|in:admin,coach,swimmer,parent',
            'address' => 'required|string',
            'postcode' => 'required|string',
            'parentEmail' => 'sometimes|required|email'
        ];
    }

    protected $stopOnFirstFailure = true;
}