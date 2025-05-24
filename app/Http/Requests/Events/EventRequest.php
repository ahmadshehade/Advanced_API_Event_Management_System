<?php

namespace App\Http\Requests\Events;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class EventRequest extends FormRequest
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
        return [
            'event_type_id' => ['required', 'exists:event_types,id'],
            'location_id' => ['required', 'exists:locations,id'],
            'title' => ['required', 'string', 'max:255', Rule::unique('events')],
            'description' => ['required', 'string'],
            'start_time' => ['required', 'date', 'after_or_equal:now'],
            'end_time' => ['required', 'date', 'after:start_time'],
            'images.*' => ['sometimes', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
    public function messages()
    {
        return [
            'event_type_id.required' => 'The event type is required.',
            'event_type_id.exists' => 'The selected event type does not exist.',

            'location_id.required' => 'The location is required.',
            'location_id.exists' => 'The selected location does not exist.',

            'title.required' => 'The title is required.',
            'title.string' => 'The title must be a valid string.',
            'title.max' => 'The title may not be greater than 255 characters.',
            'title.unique' => 'This title has already been used for another event.',

            'description.required' => 'The description is required.',
            'description.string' => 'The description must be a valid string.',

            'start_time.required' => 'The start time is required.',
            'start_time.date' => 'The start time must be a valid date.',
            'start_time.after_or_equal' => 'The start time must be today or later.',

            'end_time.required' => 'The end time is required.',
            'end_time.date' => 'The end time must be a valid date.',
            'end_time.after' => 'The end time must be after the start time.',

            'images.*.file' => 'Each image must be a valid file.',
            'images.*.image' => 'Each file must be an image.',
            'images.*.mimes' => 'Each image must be of type: jpg, jpeg, png, webp.',
            'images.*.max' => 'Each image may not be greater than 2MB.',
        ];
    }


    /**
     * Custom error response for unauthorized requests.
     */
    protected function failedAuthorization()
    {
        throw new HttpResponseException(response()->json([
            'message' => 'You are not authorized to create an event.'
        ], 403));
    }
}
