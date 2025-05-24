<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $table = "locations";

    protected $fillable = [
        'name',
        'address'
    ];

    public function events()
    {
        return $this->hasMany(Event::class, 'location_id', 'id');
    }
    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }
}
