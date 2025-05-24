<?php 

namespace App\Interfaces\Location;

interface LocationInterface{

    public function index();


    public function store($request);


    public function update($id,$request);


    public function  show($id);


    public function destroy($id);
}