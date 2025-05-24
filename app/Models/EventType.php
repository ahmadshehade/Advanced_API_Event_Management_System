<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EventType extends Model
{
    protected  $table = "event_types";

    protected  $fillable = ['name'];

    /**
     * Summary of name
     * @return Attribute
     */
    public function name(): Attribute
    {
        return Attribute::make(
            get: fn($value) => ucwords($value),
            set: fn($value) => strtolower($value),
        );
    }
    /**
     * Summary of events
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Event, EventType>
     */
    public function events()
    {
        return $this->hasMany(Event::class, 'event_type_id', 'id');
    }
    /**
     * Summary of image
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne<Image, EventType>
     */
    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }


    /**
     * Summary of scopeWithImage
     * @param mixed $query
     */
    public function  scopeWithImage($query)
    {
        return $query->with('image');
    }
}
