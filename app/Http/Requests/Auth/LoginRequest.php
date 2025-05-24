<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'min:6', 'max:32']
        ];
    }

    public function messages()
    {
        return [

            //email
            'email.required' => 'email must be required',
            'email.email' => 'email  must be email',
            'email.exists' => 'email must be exists in table  users',
            //password
            'password.required' => 'password must be required',

            'password.min' => 'password must be over 6 char',
            'password.max' => 'password must be under 32 char',
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'User Name',
            'email' => 'User Email',
            'password' => 'User password'
        ];
    }


    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'errorMessage' => $validator->errors()
            ], 422)
        );
    }
}
