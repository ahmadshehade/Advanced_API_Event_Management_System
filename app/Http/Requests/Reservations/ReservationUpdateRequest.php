<?php

namespace App\Http\Requests\Reservations;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\Event;
use App\Models\Reservation;

class ReservationUpdateRequest extends FormRequest
{
    /**
     * Summary of authorize
     * @return bool
     */
    public function authorize(): bool
    {
        $user = auth('api')->user();
        return $user && (
            $user->hasRole('adminRole') ||
            ($user->hasRole('userRole') && $user->can('edit reservation'))
        );
    }

    /**
     * Summary of rules
     * @return array{event_id: string[], seats_reserved: string[]}
     */
    public function rules(): array
    {
        return [
            'event_id' => ['sometimes', 'integer', 'exists:events,id'],
            'seats_reserved' => ['required', 'integer', 'min:1'],
        ];
    }

    /**
     * Summary of withValidator
     * @param mixed $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $reservationId = $this->route('id');
            $eventId = $this->input('event_id');
            $seatsRequested = $this->input('seats_reserved');

            $reservation = Reservation::find($reservationId);

            if (!$reservation) {
                $validator->errors()->add('reservation', 'The reservation you are trying to update does not exist.');
                return;
            }

            if (!$eventId) {
                $eventId = $reservation->event_id;
            }

            if ($eventId && $seatsRequested) {
                $event = Event::find($eventId);

                if ($event) {
                    $maxSeats = $event->max_seats;

                    $reservedSeats = $event->reservations()
                        ->where('status', '!=', 'cancelled')
                        ->where('id', '!=', $reservationId)
                        ->sum('seats_reserved');

                    $availableSeats = $maxSeats - $reservedSeats;

                    if ($availableSeats < $seatsRequested) {
                        $validator->errors()->add('seats_reserved', "Only {$availableSeats} seats are available for this event.");
                    }
                    if ($event->status === 'ongoing') {
                        $validator->errors()->add('seats_reserved', "The event is currently ongoing. You cannot update a new reservation.");
                    }
                    if ($event->status === 'ended') {
                        $validator->errors()->add('seats_reserved', "The event is currently ended. You cannot update a new reservation.");
                    }
                }
            }
        });
    }
    /**
     * Summary of messages
     * @return array{event_id.exists: string, event_id.integer: string, seats_reserved.integer: string, seats_reserved.min: string, seats_reserved.required: string}
     */
    public function messages(): array
    {
        return [
            'event_id.integer' => 'The event ID must be an integer.',
            'event_id.exists' => 'The selected event does not exist.',

            'seats_reserved.required' => 'The number of reserved seats is required.',
            'seats_reserved.integer' => 'The number of seats must be an integer.',
            'seats_reserved.min' => 'At least one seat must be reserved.',
        ];
    }

    /**
     * Summary of failedValidation
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return never
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validation failed.',
            'errors' => $validator->errors()
        ], 422));
    }
}
