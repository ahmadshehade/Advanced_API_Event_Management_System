<?php

namespace App\Http\Requests\Events;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class EventTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth('api')->user();
        return $user && $user->hasRole('adminRole');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        

       return [
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:event_types,name',
            ],
            'image' => [
                'sometimes',
                'file',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ];
    }

    /**
     * Custom error messages for validation.
     */
    public function messages(): array
    {
        return [
             'name.required' => 'The event type name is required.',
            'name.string' => 'The event type name must be a string.',
            'name.max' => 'The event type name must not exceed 255 characters.',
            'name.unique' => 'The event type name has already been taken.',

            'image.image' => 'The uploaded file must be an image.',
            'image.mimes' => 'Allowed image types are: jpg, jpeg, png, webp.',
            'image.max' => 'The image must not be larger than 2MB.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
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
