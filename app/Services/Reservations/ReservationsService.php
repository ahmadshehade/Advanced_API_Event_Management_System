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
            $reservations = Reservation::with([
                'user',
                'event'
            ])->get();
        } else {
            $reservations = Reservation::with([
                'user',
                'event'
            ])->where('user_id', $user->id)->get();
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
                throw new HttpResponseException(response()->json([
                    'message' => 'Unauthorized to create reservation',
                ], 403));
            }
            $reservation = new Reservation();
            $reservation->fill($validatedData);
            $reservation->user_id = auth('api')->user()->id;
            $reservation->save();
            $data = [
                'message' => 'Successfully Make Reservation ',
                'data' => $reservation->with(['user', 'event'])->find($reservation->id),
                'code' => 201
            ];
            return $data;
        } catch (Exception  $e) {
            throw new HttpResponseException(
                response()->json([
                    'message' => $e->getMessage()
                ], 500)
            );
        }
    }

    /**
     * Summary of update
     * @param mixed $id
     * @param mixed $request
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return array{code: int, data: Reservation|\Illuminate\Database\Eloquent\Collection<int, Reservation>, message: string}
     */
    public function update($id, $request)
    {
        try {
            $reservation = Reservation::find($id);

            if (!$reservation) {
                throw new HttpResponseException(response()->json([
                    'message' => 'Reservation not found!',
                ], 404));
            }



            if (!Gate::allows('update', $reservation)) {
                throw new HttpResponseException(response()->json([
                    'message' => 'Unauthorized to update this reservation',
                ], 403));
            }

            $validatedData = $request->validate([
                'event_id' => ['sometimes', 'integer', 'exists:events,id'],
                'seats_reserved' => ['required', 'integer', 'min:1'],
            ]);

            $reservation->update($validatedData);
            $reservation->load(['user', 'event']);

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
            throw new HttpResponseException(response()->json([
                'message' => 'Unauthorized to delete this reservation',
            ], 403));
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
        $reservation = Reservation::find($id);
        if (!$reservation) {
            throw new HttpResponseException(
                response()->json([
                    'message' => 'reservation not found !'
                ], 404)
            );
        }


        if (!Gate::allows('view', $reservation)) {
            throw new HttpResponseException(response()->json([
                'message' => 'Unauthorized to view this reservation',
            ], 403));
        }

        $data = [
            'message' => 'Successfully get Reservation ',
            'data' => $reservation->with(['user', 'event'])->find($reservation->id),
            'code' => 200
        ];
        return $data;
    }
}
