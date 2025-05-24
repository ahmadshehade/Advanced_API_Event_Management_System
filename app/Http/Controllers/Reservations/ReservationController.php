<?php

namespace App\Http\Controllers\Reservations;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reservations\ReservationStoreRequest;
use App\Http\Requests\Reservations\ReservationUpdateRequest;
use App\Interfaces\Reservations\ReservationsInterface;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    protected $reservation;

    /**
     * Summary of __construct
     * @param \App\Interfaces\Reservations\ReservationsInterface $reservation
     */
    public function __construct(ReservationsInterface $reservation)
    {
        $this->reservation = $reservation;
    }


    /**
     * Summary of index
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $data = $this->reservation->index();
        return $this->getMessage($data['message'], $data['data'], $data['code']);
    }


    /**
     * Summary of store
     * @param \App\Http\Requests\Reservations\ReservationStoreRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function store(ReservationStoreRequest $request)
    {
        $data = $this->reservation->store($request);
        return $this->getMessage($data['message'], $data['data'], $data['code']);
    }


    /**
     * Summary of update
     * @param mixed $id
     * @param \App\Http\Requests\Reservations\ReservationUpdateRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function update($id, ReservationUpdateRequest $request)
    {
        $data = $this->reservation->update($id, $request);
        return $this->getMessage($data['message'], $data['data'], $data['code']);
    }

    /**
     * Summary of destroy
     * @param mixed $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $data = $this->reservation->destroy($id);
        return $this->getMessage($data['message'], $data['data'], $data['code']);
    }
    /**
     * Summary of show
     * @param mixed $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public  function show($id)
    {
        $data = $this->reservation->show($id);
        return $this->getMessage($data['message'], $data['data'], $data['code']);
    }
}
