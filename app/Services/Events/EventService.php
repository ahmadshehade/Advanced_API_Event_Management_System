<?php

namespace App\Services\Events;

use App\Interfaces\Events\EventInterface;
use App\Models\Event;
use App\Traits\ManagerFile;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class EventService implements EventInterface
{
    use ManagerFile;
    /**
     * Summary of index
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return array{code: int, data: \Illuminate\Database\Eloquent\Collection<int, Event>, message: string}
     */
    public  function index()
    {
        $user = auth('api')->user();
        if (!Gate::allows('viewAny', Event::class)) {
            throw new HttpResponseException(response()->json([
                'message' => 'Unauthorized to get All Event',
            ], 403));
        }
        if ($user->hasRole('adminRole')) {
            $events = Event::withDetails()->get();
        } else {
            $events = Event::withDetails()->where('status', 'upcoming')->get();
        }
        if ($events->isEmpty()) {
            throw  new HttpResponseException(
                response()->json([
                    'message' => 'Events  not Found !',
                ], 404)
            );
        }
        $data = [
            'message' => 'Successfully Get all Event with Count :' . $events->count(),
            'data' => $events,
            'code' => 200
        ];

        return $data;
    }

    /**
     * Summary of store
     * @param mixed $request
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return array{code: int, data: Event, message: string}
     */
    public function store($request)
    {
        try {
            DB::beginTransaction();

            $validatedData = $request->validated();

            $event = new Event();
            $event->event_type_id = $validatedData['event_type_id'];
            $event->location_id = $validatedData['location_id'];
            $event->title = $validatedData['title'];
            $event->slug = $validatedData['slug'];
            $event->description = $validatedData['description'];
            $event->status = $validatedData['status'];
            $event->max_seats = $validatedData['max_seats'] ?? null;
            $event->start_time = $validatedData['start_time'];
            $event->end_time = $validatedData['end_time'];
            $event->user_id = auth('api')->id();

            $event->save();

            $this->uploadImages(
                $request,
                'images',
                Event::class,
                $event->id,
                'Events/' . $event->slug
            );

            DB::commit();

            $message = 'Event created successfully';
            if ($event->wasRecentlyCreated) {
                $message = 'New Event Created: ' . $event->title;
            }

            return [
                'message' => $message,
                'data' => $event->load('images'),
                'code' => 200
            ];
        } catch (Exception $e) {
            DB::rollBack();
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
     * @return array{code: int, data: Event|\Illuminate\Database\Eloquent\Collection<int, Event>, message: string}
     */
    public function update($id, $request)
    {
        DB::beginTransaction();

        try {
            $validatedData = $request->validated();

            $event = Event::find($id);
            if (!$event) {
                throw new HttpResponseException(
                    response()->json([
                        'message' => 'Event not found!',
                    ], 404)
                );
            }

            if (isset($validatedData['event_type_id'])) {
                $event->event_type_id = $validatedData['event_type_id'];
            }

            if (isset($validatedData['location_id'])) {
                $event->location_id = $validatedData['location_id'];
            }

            if (isset($validatedData['title'])) {
                $event->title = $validatedData['title'];
                $event->slug = $validatedData['slug'];
            }

            if (isset($validatedData['description'])) {
                $event->description = $validatedData['description'];
            }

            if (isset($validatedData['start_time'])) {
                $event->start_time = $validatedData['start_time'];
            }

            if (isset($validatedData['end_time'])) {
                $event->end_time = $validatedData['end_time'];
            }
            if (isset($validatedData['status'])) {
                $event->status = $validatedData['status'];
                if ($validatedData['status'] === 'ongoing') {
                    $reservations = $event->reservations()->get();
                    foreach ($reservations as $reservation) {
                        $reservation->update([
                            'status' => 'confirmed',
                            'confirmed_at' => now()
                        ]);
                    }
                }
                if ($validatedData['status'] === 'ended') {
                    $reservations = $event->reservations()->get();
                    foreach ($reservations as $reservation) {
                        $reservation->update([
                            'status' => 'cancelled',
                            'confirmed_at' => null
                        ]);
                    }
                }
            }
            if (isset($validatedData['max_seats'])) {
                $event->max_seats = $validatedData['max_seats'];
            }

            if ($event->isDirty()) {
                $event->save();
            }

            if ($request->hasFile('newImages')) {
                $imagesId = $event->images->pluck('id')->toArray();

                $this->deleteImages(
                    Event::class,
                    $event->id,
                    $imagesId
                );

                $this->uploadImages(
                    $request,
                    'newImages',
                    Event::class,
                    $event->id,
                    'Events/' . $event->title
                );
            }

            DB::commit();

            return [
                'message' => 'Successfully updated event.',
                'data' => $event->load('images'),
                'code' => 200
            ];
        } catch (Exception $e) {
            DB::rollBack();
            throw new HttpResponseException(
                response()->json([
                    'message' => $e->getMessage()
                ], 500)
            );
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
        $event = Event::where('id', $id)->first();
        if (!$event) {
            throw  new HttpResponseException(
                response()->json([
                    'message' => 'Event  not Found !',
                ], 404)
            );
        }
        $imagesId = $event->images->pluck('id')->toArray();
        $this->deleteImages(
            Event::class,
            $event->id,
            $imagesId
        );
        $event->delete();
        $data = [
            'message' => 'Successfully delete Event',
            'data' => true,
            'code' => 200
        ];

        return $data;
    }

    /**
     * Summary of show
     * @param mixed $id
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return array{code: int, data: Event, message: string}
     */
    public function show($id)
    {
        $user = auth('api')->user();
        if ($user->hasRole('adminRole')) {
            $event = Event::withDetails()->where('id', $id)->first();
        } else {
            $event = Event::withDetails()
                ->where('id', $id)
                ->where('status', 'upcoming')->first();
        }
        if (!$event) {
            throw  new HttpResponseException(
                response()->json([
                    'message' => 'Event  not Found !',
                ], 404)
            );
        }
        if (!Gate::allows('view', $event)) {
            throw new HttpResponseException(response()->json([
                'message' => 'Unauthorized to get  Event',
            ], 403));
        }
        $data = [
            'message' => 'Successfully get Event',
            'data' => $event,

            'code' => 200
        ];

        return $data;
    }
}
