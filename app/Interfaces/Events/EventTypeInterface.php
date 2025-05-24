<?php 

namespace  App\Interfaces\Events;

interface EventTypeInterface{

    public function  store($request);


    public function update($id,$request);


    public function destroy($id);


    public function  show($id);


    public function getEventTypes();
}