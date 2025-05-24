<?php

namespace App\Http\Controllers\Events;

use App\Http\Controllers\Controller;
use App\Http\Requests\Events\EventTypeRequest;
use App\Http\Requests\Events\UpdateEventTypeRequest;
use App\Interfaces\Events\EventTypeInterface;
use Illuminate\Http\Request;

class EventTypeController extends Controller
{
    protected $eventType;

    /**
     * Summary of __construct
     * @param \App\Interfaces\Events\EventTypeInterface $eventType
     */
    public function __construct(EventTypeInterface $eventType)
    {
        $this->eventType = $eventType;
    }

    /**
     * Summary of index
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $data = $this->eventType->getEventTypes();
        return $this->getMessage($data['message'], $data['data'], $data['code']);
    }

    /**
     * Summary of store
     * @param \App\Http\Requests\Events\EventTypeRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function store(EventTypeRequest $request)
    {
        $data = $this->eventType->store($request);
        return $this->getMessage($data['message'], $data['data'], $data['code']);
    }


  

    /**
     * Summary of update
     * @param mixed $id
     * @param \App\Http\Requests\Events\EventTypeRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function update($id, UpdateEventTypeRequest  $request)
    {
        $data = $this->eventType->update($id, $request);
        return  $this->getMessage($data['message'], $data['data'], $data['code']);
    }

    /**
     * Summary of show
     * @param mixed $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $data = $this->eventType->show($id);
        return  $this->getMessage($data['message'], $data['data'], $data['code']);
    }

    /**
     * Summary of destroy
     * @param mixed $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $data = $this->eventType->destroy($id);
        return $this->getMessage($data['message'], $data['data'], $data['code']);
    }
}
