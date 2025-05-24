<?php

namespace App\Http\Requests\Events;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateEventTypeRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
     public function rules(): array
    {
        $eventTypeId = $this->route('id');

        return [
            'name' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('event_types', 'name')->ignore($eventTypeId),
            ],
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
            'name.string' => 'The event type name must be a string.',
            'name.max' => 'The event type name must not exceed 255 characters.',
            'name.unique' => 'The event type name has already been taken.',

            'newImage.image' => 'The new image must be a valid image file.',
            'newImage.mimes' => 'Allowed image types for the new image are: jpg, jpeg, png, webp.',
            'newImage.max' => 'The new image must not be larger than 2MB.',
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
