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
            'firstName' => 'sometimesrequired|max:50|string',
            'lastName' => 'sometimesrequired|max:50|string',
            'phoneNumber' => 'sometimesrequired',
            'emailAddress' => 'sometimesrequired|email',
            'pictureUrl' => 'sometimes|required|url',
            'gender' => 'sometimesrequired|in:male,female,others',
            'address' => 'sometimesrequired|string',
            'postcode' => 'sometimesrequired|string',
        ];
    }

    protected $stopOnFirstFailure = true;
}
