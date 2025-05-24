<?php

namespace App\Http\Requests\Locations;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth('api')->user();
        return $user && $user->hasRole('adminRole'); 
    }

    public function rules(): array
    {
        $locationId = $this->route('id');

        return [
            'name' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('locations')->ignore($locationId),
            ],
            'address' => ['required', 'string', 'max:255'],
            'newImage' => [
                'sometimes',
                'file',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'Name must be a string.',
            'name.max' => 'Name must not exceed 255 characters.',
            'name.unique' => 'This name already exists.',
            'address.required' => 'Address is required.',
            'address.string' => 'Address must be a string.',
            'newImage.image' => 'The new image must be a valid image file.',
            'newImage.mimes' => 'Allowed image types: jpg, jpeg, png, webp.',
            'newImage.max' => 'The image must not be larger than 2MB.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422)
        );
    }
}
