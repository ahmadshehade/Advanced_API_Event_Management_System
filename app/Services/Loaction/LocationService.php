<?php

namespace App\Services\Loaction;

use App\Interfaces\Location\LocationInterface;
use App\Models\Location;
use App\Traits\ManagerFile;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class LocationService implements LocationInterface
{

    use ManagerFile;
    /**
     * Summary of index
     * @return array{code: int, data: \Illuminate\Database\Eloquent\Collection<int, Location>, message: string}
     */
    public function index()
    {
        if (!Gate::allows('viewAny', Location::class)) {
            throw new HttpResponseException(response()->json([
                'message' => 'Unauthorized to get All Location',
            ], 403));
        }
        $locations = Location::with('image')->get();
        $data = [
            'message' => 'Get All Location',
            'data' => $locations,
            'code' => 200
        ];
        return $data;
    }

    /**
     * Summary of store
     * @param mixed $request
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return array{code: int, data: Location, messsge: string}
     */
    public function store($request)
    {
        try {
            DB::beginTransaction();
            if (!Gate::allows('create', Location::class)) {
                throw new HttpResponseException(response()->json([
                    'message' => 'Unauthorized to create Location',
                ], 403));
            }
            $validatedData = $request->validated();
            $loction = Location::create([
                'name' => $validatedData['name'],
                'address' => $validatedData['address']
            ]);
            $this->uploadImages(
                $request,
                'image',
                Location::class,
                $loction->id,
                'Locations/' . $loction->name . '-' . $loction->id
            );

            DB::commit();
            $data = [
                'message' => 'Successfully Create New Location',
                'data' => $loction->load('image'),
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
     * @return array{code: int, data: Location|\Illuminate\Database\Eloquent\Collection<int, Location>, message: string}
     */
    public function update($id, $request)
    {
        try {
            DB::beginTransaction();
            $validatedData = $request->validated();

            $location = Location::find($id);
            if (!$location) {
                throw new HttpResponseException(
                    response()->json([
                        'message' => 'Loaction Not Found !'
                    ], 404)
                );
            }
            if (!Gate::allows('update', $location)) {
                throw new HttpResponseException(response()->json([
                    'message' => 'Unauthorized to update Location',
                ], 403));
            }
            if (array_key_exists('name', $validatedData)) {
                $location->name = $validatedData['name'];
            }

            $location->address = $validatedData['address'];

            $location->save();

            if ($request->hasfile('newImage')) {
                $this->deleteImages(
                    Location::class,
                    $location->id,
                    $location->image->id
                );
                $this->uploadImages(
                    $request,
                    'newImage',
                    Location::class,
                    $location->id,
                    'Locations/' . $location->name . '-' . $location->id
                );
            }
            DB::commit();
            $data = [
                'message' => 'Successfully  Update Location !',
                'data' => $location->load('image'),
                'code' => 200
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
     * Summary of show
     * @param mixed $id
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return array{code: int, data: Location|\Illuminate\Database\Eloquent\Collection<int, Location>, message: string}
     */
    public function  show($id)
    {

        $location = Location::find($id);
        if (!$location) {
            throw new HttpResponseException(
                response()->json([
                    'message' => 'Loaction Not Found !'
                ], 404)
            );
        }
        if (!Gate::allows('view', $location)) {
            throw new HttpResponseException(response()->json([
                'message' => 'Unauthorized to get Location',
            ], 403));
        }
        $data = [
            'message' => 'Successfully Get Location',
            'data' => $location->load('image'),
            'code' => 200
        ];
        return $data;
    }

    /**
     * Summary of destroy
     * @param mixed $id
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return array{data: bool, message: string}
     */
    public function destroy($id)
    {

        $location = Location::find($id);
        if (!$location) {
            throw new HttpResponseException(
                response()->json([
                    'message' => 'Loaction Not Found !'
                ], 404)
            );
        }
        if (!Gate::allows('view', $location)) {
            throw new HttpResponseException(response()->json([
                'message' => 'Unauthorized to delete Location',
            ], 403));
        }
        $this->deleteImages(
            Location::class,
            $location->id,
            $location->image->id,
        );
        $location->delete();
        $data = [
            'message' => 'Successfully  Deleted location',
            'data' => true,
            'code' => 200
        ];
        return $data;
    }
}
