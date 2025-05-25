<?php

namespace App\Services\Reservations;

use App\Interfaces\Reservations\ReservationsInterface;
use App\Models\Reservation;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Gate;

class ReservationsService implements ReservationsInterface
{

    /**
     * Summary of index
     * @return array{code: int, data: \Illuminate\Database\Eloquent\Collection<int, Reservation>, message: string}
     */
    public function index()
    {
        $user = auth('api')->user();

        if ($user->hasRole('adminRole')) {
            $reservations = Reservation::withRelations()->get();
        } else {
            $reservations = Reservation::withRelations()
                ->ReservationNotCancelled()->where('user_id', $user->id)->get();
        }


        $data = [
            'message' => 'Successfully get reservations',
            'data' => [
                'reservation' => $reservations,
            ],
            'code' => 200,
        ];
        return $data;
    }


    /**
     * Summary of store
     * @param mixed $request
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return array{code: int, data: Reservation|\Illuminate\Database\Eloquent\Collection<int, Reservation>|null, message: string}
     */
    public function store($request)
    {
        try {
            $validatedData = $request->validated();

            if (!Gate::allows('create', Reservation::class)) {
                abort(403, 'Unauthorized to create this reservation');
            }

            $reservation = new Reservation();
            $reservation->fill($validatedData);
            $reservation->user_id = auth('api')->id();
            $reservation->save();

            $reservation = $reservation->load(['user', 'event']);

            $data = [
                'message' => 'Successfully made reservation',
                'data' => $reservation,
                'code' => 201
            ];
            return $data;
        } catch (Exception $e) {
            throw new HttpResponseException(response()->json([
                'message' => $e->getMessage()
            ], 500));
        }
    }


    /**
     * Summary of update
     * @param mixed $id
     * @param mixed $request
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function update($id,  $request)
    {
        try {
            $reservation = Reservation::where('id', $id)
                ->reservationNotCancelled()->first();
            if (!$reservation) {
                abort(404, 'Reservation not found Or Reservation is cancelled!');
            }

            if (!Gate::allows('update', $reservation)) {
                abort(403, 'Unauthorized to update this reservation');
            }

            $validatedData = $request->validated();

            $reservation->update($validatedData);

            $reservation->load(['event', 'user']);

            $data = [
                'message' => 'Successfully updated reservation',
                'data' => $reservation,
                'code' => 200,
            ];
            return $data;
        } catch (Exception $e) {
            throw new HttpResponseException(response()->json([
                'message' => $e->getMessage(),
            ], 500));
        }
    }

    /**
     * Summary of destroy
     * @param mixed $id
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return array{code: int, data: bool, message: string}
     */
    public function destroy($id)
    {
        $reservation = Reservation::find($id);
        if (!$reservation) {
            throw new HttpResponseException(
                response()->json([
                    'message' => 'reservation not found !'
                ], 404)
            );
        }
        $user = auth('api')->user();

        if (!Gate::allows('delete', $reservation)) {
            abort(403, 'Unauthorized to delete this reservation');
        }
        $reservation->delete();
        $data = [
            'message' => 'Successfully Delete Reservation ',
            'data' => true,
            'code' => 200
        ];
        return $data;
    }


    /**
     * Summary of show
     * @param mixed $id
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return array{code: int, data: Reservation|\Illuminate\Database\Eloquent\Collection<int, Reservation>|null, message: string}
     */
    public function show($id)
    {
        $user = auth('api')->user();
        $reservation = Reservation::withRelations()->find($id);
        if (!$reservation) {
            throw new HttpResponseException(
                response()->json([
                    'message' => 'reservation not found !'
                ], 404)
            );
        }
        if (!Gate::allows('view', $reservation)) {
            abort(403, 'Unauthorized to get this reservation');
        }
        if ($user->hasRole('userRole')) {
            if (($reservation->event->status === 'ended') && ($reservation->status === 'cancelled')) {
                throw new HttpResponseException(
                    response()->json([
                        'message' => 'Sorry, the event has ended!'
                    ], 403)
                );
            }
        }

        $data = [
            'message' => 'Successfully get Reservation ',
            'data' => $reservation,
            'code' => 200
        ];
        return $data;
    }
}
