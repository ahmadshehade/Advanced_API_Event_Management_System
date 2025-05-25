<?php

namespace App\Http\Requests\Events;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

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
     * Summary of prepareForValidation
     * @return void
     */
    protected function prepareForValidation(): void
    {
        if (!$this->filled('slug') && $this->has('title')) {
            $baseSlug = Str::slug($this->input('title'));
            $slug = $baseSlug;
            $counter = 1;

            while (DB::table('events')->where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }

            $this->merge(['slug' => $slug]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'event_type_id' => ['required', 'exists:event_types,id'],
            'location_id' => ['required', 'exists:locations,id'],
            'title' => ['required', 'string', 'max:255', Rule::unique('events')],
            'slug' => ['string', 'max:255', Rule::unique('events')],
            'description' => ['required', 'string'],
            'status' => ['required', Rule::in(['upcoming', 'ongoing', 'ended'])],
            'max_seats' => ['nullable', 'integer', 'min:1'],
            'start_time' => ['required', 'date', 'after_or_equal:now'],
            'end_time' => ['required', 'date', 'after:start_time'],
            'images.*' => ['sometimes', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
    /**
     * Summary of messages
     * @return array{description.required: string, description.string: string, end_time.after: string, end_time.date: string, end_time.required: string, event_type_id.exists: string, event_type_id.required: string, images.*.file: string, images.*.image: string, images.*.max: string, images.*.mimes: string, location_id.exists: string, location_id.required: string, max_seats.integer: string, max_seats.min: string, slug.max: string, slug.required: string, slug.string: string, slug.unique: string, start_time.after_or_equal: string, start_time.date: string, start_time.required: string, status.in: string, status.required: string, title.max: string, title.required: string, title.string: string, title.unique: string}
     */
    public function messages(): array
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

            'slug.required' => 'The slug is required.',
            'slug.string' => 'The slug must be a valid string.',
            'slug.max' => 'The slug must not be greater than 255 characters.',
            'slug.unique' => 'This slug is already in use.',

            'description.required' => 'The description is required.',
            'description.string' => 'The description must be a valid string.',

            'status.required' => 'The event status is required.',
            'status.in' => 'The status must be one of: upcoming, ongoing, or ended.',

            'max_seats.integer' => 'The max seats must be an integer.',
            'max_seats.min' => 'The max seats must be at least 1.',

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
    protected function failedAuthorization(): void
    {
        throw new HttpResponseException(response()->json([
            'message' => 'You are not authorized to create an event.'
        ], 403));
    }

    /**
     * Summary of failedValidation
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return never
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
