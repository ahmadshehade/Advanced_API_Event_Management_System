<?php

namespace App\Http\Requests\Events;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateEeventRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth('api')->user();

        // Only users with 'adminRole' can update events
        return $user && $user->hasRole('adminRole');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $eventId = $this->route('id');
        return [
            'event_type_id' => ['sometimes', 'exists:event_types,id'],
            'location_id' => ['sometimes', 'exists:locations,id'],
            'title' => ['sometimes', 'string', 'max:255', Rule::unique('events')->ignore($eventId)],
            'description' => ['sometimes', 'string'],
            'start_time' => ['sometimes', 'date', 'after_or_equal:now'],
            'end_time' => ['sometimes', 'date', 'after:start_time'],
            'newImages.*' => ['sometimes', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

        ];
    }

    /**
     * Custom error messages for validation.
     */
    public function messages()
    {
        return [
            'event_type_id.exists' => 'The selected event type does not exist.',
            'location_id.exists' => 'The selected location does not exist.',

            'title.string' => 'The title must be a valid string.',
            'title.max' => 'The title may not be greater than 255 characters.',
            'title.unique' => 'This event title has already been taken.',

            'description.string' => 'The description must be a valid string.',

            'start_time.date' => 'The start time must be a valid date.',
            'start_time.after_or_equal' => 'The start time must be today or later.',

            'end_time.date' => 'The end time must be a valid date.',
            'end_time.after' => 'The end time must be after the start time.',

            'newImages.*.image' => 'Each file must be an image.',
            'newImages.*.file' => 'Each image must be a valid file.',
            'newImages.*.max' => 'Each image may not be greater than 2MB.',
            'newImages.*.mimes' => 'Each image must be of type: jpg, jpeg, png, webp.',
        ];
    }


    /**
     * Custom response for failed authorization.
     */
    protected function failedAuthorization()
    {
        throw new HttpResponseException(response()->json([
            'message' => 'You are not authorized to update this event.'
        ], 403));
    }
}
