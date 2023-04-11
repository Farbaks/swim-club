<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordDto extends FormRequest
{
   /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'oldPassword' => 'required|min:8|string',
            'newPassword' => 'required|min:8|string',
        ];
    }

    protected $stopOnFirstFailure = true;
}
