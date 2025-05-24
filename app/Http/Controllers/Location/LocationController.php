<?php

namespace App\Http\Controllers\Location;

use App\Http\Controllers\Controller;
use App\Http\Requests\Locations\StoreLocationRequest;
use App\Http\Requests\Locations\UpdateLocationRequest;
use App\Interfaces\Location\LocationInterface;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    protected  $location;

    /**
     * Summary of __construct
     * @param \App\Interfaces\Location\LocationInterface $location
     */
    public function __construct(LocationInterface $location)
    {
        $this->location = $location;
    }

    /**
     * Summary of index
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $data = $this->location->index();
        return $this->getMessage($data['message'], $data['data'], $data['code']);
    }

  
    /**
     * Summary of store
     * @param \App\Http\Requests\Locations\StoreLocationRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function store(StoreLocationRequest $request)
    {
        $data = $this->location->store($request);
        return $this->getMessage($data['message'], $data['data'], $data['code']);
    }

   
    /**
     * Summary of update
     * @param mixed $id
     * @param \App\Http\Requests\Locations\UpdateLocationRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function update($id, UpdateLocationRequest $request)
    {
        $data = $this->location->update($id, $request);
        return $this->getMessage($data['message'], $data['data'], $data['code']);
    }

    /**
     * Summary of destroy
     * @param mixed $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $data = $this->location->destroy($id);
        return $this->getMessage($data['message'], $data['data'], $data['code']);
    }

    /**
     * Summary of show
     * @param mixed $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $data = $this->location->show($id);
        return $this->getMessage($data['message'], $data['data'], $data['code']);
    }
}
