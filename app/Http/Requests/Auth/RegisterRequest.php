<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth('api')->user();
        return $user->hasRole('adminRole');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:32'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:6', 'max:32'],
            'image' => ['sometimes', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048']
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'name must be required',
            'name.string' => 'name must be string',
            'name.max' => 'name must be under 32 char',
            //email
            'email.required' => 'email must be required',
            'email.email' => 'email  must be email',
            'email.unique' => 'email must be unique in table  users',
            //password
            'password.required' => 'password must be required',
            'password.confirmed' => 'password must be  confirmed ',
            'password.min' => 'password must be over 6 char',
            'password.max' => 'password must be under 32 char',
            //image

            'image.image' => 'The file must be an image.',
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, svg.',
            'image.max' => 'The image may not be greater than 2 megabytes.',
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
