<?php

namespace App\Services\Events;

use App\Interfaces\Events\EventTypeInterface;
use App\Models\Event;
use App\Models\EventType;
use App\Traits\ManagerFile;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class EventTypeService implements EventTypeInterface
{
    use ManagerFile;
    /**
     * Summary of store
     * @param mixed $request
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return array{code: int, data: EventType, message: string}
     */
    public function  store($request)
    {
        try {
            DB::beginTransaction();
           
            $validatedData = $request->validated();
            $eventType = EventType::firstOrCreate([
                'name'=>$validatedData['name']
            ]);
            if ($eventType->wasRecentlyCreated) {
                Log::info('Successfully Add New EventType');
            }

            $this->uploadImages(
                $request,
                'image',
                EventType::class,
                $eventType->id,
                'Event Type/' . $eventType->name . '-' . $eventType->id
            );
            DB::commit();

            $data = [
                'message' => 'Successfully  Create Event Type ',
                'data' => [$eventType, $eventType->image->url],
                'code' => 201
            ];
            return $data;
        } catch (Exception  $e) {
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
     * @return array{code: int, data: EventType|\Illuminate\Database\Eloquent\Collection<int, EventType>, message: string}
     */
    public function update($id, $request)
    {
        try {
            DB::beginTransaction();
            $validatedData = $request->validated();
            $eventType = EventType::find($id);

            if (!$eventType) {
                throw  new HttpResponseException(
                    response()->json([
                        'message' => 'Event Type not Found !',
                    ], 404)
                );
            }
            $eventType->update([
                'name'=>$validatedData['name'],
            ]);

            if ($request->hasfile('newImage')) {
                $this->deleteImages(
                    EventType::class,
                    $eventType->id,
                    $eventType->image->id
                );
                $this->uploadImages(
                    $request,
                    'newImage',
                    EventType::class,
                    $eventType->id,
                    'Event Type/' . $eventType->name . '-' . $eventType->id
                );
            }
            $data = [
                'message' => 'Successfully Update Event Type',
                'data' => $eventType->load('image'),
                'code' => 200
            ];
            DB::commit();

            return $data;
        } catch (Exception  $e) {
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
        $eventType = EventType::find($id);
        if (!$eventType) {
            throw  new HttpResponseException(
                response()->json([
                    'message' => 'Event Type not Found !',
                ], 404)
            );
        }
        $imageIds = $eventType->image->id;

        $this->deleteImages(
            EventType::class,
            $eventType->id,
            $imageIds
        );

        $eventType->delete();
        $data = [
            'message' => 'Successfully  Deleted EventType',
            'data' => true,
            'code' => 200
        ];
        return $data;
    }

    /**
     * Summary of show
     * @param mixed $id
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return array{code: int, data: EventType|\Illuminate\Database\Eloquent\Collection<int, EventType>, message: string}
     */
    public function  show($id)
    {
        $eventType = EventType::WithImage()->find($id);
        if (!$eventType) {
            throw  new HttpResponseException(
                response()->json([
                    'message' => 'Event Type not Found !',
                ], 404)
            );
        }
        if (!Gate::allows('view', $eventType)) {
            throw new HttpResponseException(response()->json([
                'message' => 'Unauthorized to View Event Type',
            ], 403));
        }
        $data = [
            'message' => 'Successfully Get The Event Type',
            'data' => $eventType->load('image'),
            'code' => 200
        ];
        return $data;
    }

    /**
     * Summary of getEventTypes
     * @return array{code: int, data: \Illuminate\Database\Eloquent\Collection<int, EventType>, message: string}
     */
    public function getEventTypes()
    {
        $eventTypes = EventType::withImage()->get();
        if (!Gate::allows('viewAny', EventType::class)) {
            throw new HttpResponseException(response()->json([
                'message' => 'Unauthorized to get All Event Type',
            ], 403));
        }
        $data = [
            'message' => 'Successfully Get All Event Type',
            'data' => $eventTypes,
            'code' => 200
        ];
        return $data;
    }
}
