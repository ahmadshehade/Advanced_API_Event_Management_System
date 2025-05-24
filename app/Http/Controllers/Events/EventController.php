<?php

namespace App\Http\Controllers\Events;

use App\Http\Controllers\Controller;
use App\Http\Requests\Events\EventRequest;
use App\Http\Requests\Events\UpdateEeventRequest;
use App\Interfaces\Events\EventInterface;
use Illuminate\Http\Request;

class EventController extends Controller
{

    protected $event;

    /**
     * Summary of __construct
     * @param \App\Interfaces\Events\EventInterface $event
     */
    public function __construct(EventInterface $event)
    {
        $this->event = $event;
    }
    /**
     * Summary of index
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $data = $this->event->index();
        return $this->getMessage($data['message'], $data['data'], $data['code']);
    }

    /**
     * Summary of store
     * @param \App\Http\Requests\Events\EventRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function store(EventRequest $request)
    {
        $data = $this->event->store($request);
        return $this->getMessage($data['message'], $data['data'], $data['code']);
    }
    /**
     * Summary of update
     * @param mixed $id
     * @param \App\Http\Requests\Events\UpdateEeventRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function update($id, UpdateEeventRequest $request)
    {
        $data = $this->event->update($id, $request);
        return $this->getMessage($data['message'], $data['data'], $data['code']);
    }
    /**
     * Summary of destroy
     * @param mixed $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $data = $this->event->destroy($id);
        return $this->getMessage($data['message'], $data['data'], $data['code']);
    }

    /**
     * Summary of show
     * @param mixed $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $data = $this->event->show($id);
        return $this->getMessage($data['message'], $data['data'], $data['code']);
    }
}
