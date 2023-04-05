<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class SigninUserDto extends FormRequest
{
     /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'emailAddress' => 'required|max:255',
            'password' => 'required|min:8',
        ];
    }

    protected $stopOnFirstFailure = true;
}
