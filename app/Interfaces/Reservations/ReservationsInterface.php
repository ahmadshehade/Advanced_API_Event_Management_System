<?php 
namespace App\Interfaces\Reservations;

interface ReservationsInterface{
    public function index();

    public function store($request);


    public function update($id,$request);

    public function destroy($id);


    public function show($id);

}