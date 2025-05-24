<?php

namespace App\Http\Requests\Reservations;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ReservationStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth('api')->user();
        return  $user->hasRole('adminRole') ||
            ($user->hasRole('userRole') && $user->can('create reservation'));
    }

    public function rules(): array
    {
        return [
            'event_id' => ['required', 'integer', 'exists:events,id'],
            'seats_reserved' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'event_id.required' => 'The event is required.',
            'event_id.integer' => 'The event ID must be an integer.',
            'event_id.exists' => 'The selected event does not exist.',

            'seats_reserved.required' => 'The number of seats is required.',
            'seats_reserved.integer' => 'The number of seats must be an integer.',
            'seats_reserved.min' => 'You must reserve at least 0 seats.',
        ];
    }

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
