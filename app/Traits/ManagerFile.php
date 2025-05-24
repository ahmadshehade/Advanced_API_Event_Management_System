<?php

namespace App\Traits;

use App\Models\Image;
use Illuminate\Support\Facades\Storage;

trait ManagerFile
{

    public function uploadImages($request, $inputName, $imageable_type, $imageable_id, $folderPath)
    {
        if (!$request->hasFile($inputName)) {
            return;
        }

        $files = is_array($request->file($inputName))
            ? $request->file($inputName)
            : [$request->file($inputName)];

        foreach ($files as $file) {
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs($folderPath, $filename, 'public');

            Image::create([
                'url'      => $path,
                'imageable_type'  => $imageable_type,
                'imageable_id'    => $imageable_id,
            ]);
        }
    }

    public function deleteImages($imageable_type, $imageable_id, $imageIds = null)
    {

        $query = Image::where('imageable_type', $imageable_type)
            ->where('imageable_id', $imageable_id);


        if ($imageIds) {
            $query->whereIn('id', is_array($imageIds) ? $imageIds : [$imageIds]);
        }

        $images = $query->get();

        foreach ($images as $image) {

            if (Storage::disk('public')->exists($image->url)) {
                Storage::disk('public')->delete($image->url);
            }


            $image->delete();
        }
    }
}
