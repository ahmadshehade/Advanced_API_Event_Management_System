<?php

namespace App\Http\Requests\Locations;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreLocationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
       $user=auth('api')->user();
        return $user &&$user->hasRole('adminRole');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'unique:locations,name', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'image' => ['sometimes', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048']
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Location name is required.',
            'name.string' => 'Location name must be a string.',
            'name.unique' => 'Location name must be unique.',
            'name.max' => 'Location name must not exceed 255 characters.',

            'address.required' => 'Address is required.',
            'address.string' => 'Address must be a string.',
            'address.max' => 'Address must not exceed 255 characters.',

            'image.image' => 'The file must be a valid image.',
            'image.mimes' => 'Only jpg, jpeg, png, and webp images are allowed.',
            'image.max' => 'Image size must not exceed 2MB.',
        ];
    }

    /**
     * Handle failed validation.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
