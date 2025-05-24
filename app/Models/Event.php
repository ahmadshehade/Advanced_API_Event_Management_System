<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Event extends Model
{
    protected  $table = "events";

    protected $fillable = [
        'title',
        'description',
        'start_time',
        'end_time',
        'event_type_id',
        'location_id'
    ];

    protected  $guarded = ['user_id'];

    /**
     * Get the user that owns the Event
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the user that owns the Event
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function eventType()
    {
        return $this->belongsTo(EventType::class, 'event_type_id', 'id');
    }

    /**
     * Get the user that owns the Event
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }

    /**
     * Summary of scopeWithDetails
     * @param mixed $query
     */
    public function scopeWithDetails($query)
    {
        return $query->with([
            'user',
            'location',
            'eventType',
            'mainImage',
              'images',
        ])
            ->withCount(['reservations', 'images'])
            ->withSum('reservations', 'seats_reserved');
    }

    /**
     * Summary of title
     * @return Attribute
     */
    protected function title(): Attribute
    {

        return Attribute::make(
            get: fn($value) => ucwords($value),
            set: fn($value) => strtolower($value),
        );
    }

    /**
     * Summary of description
     * @return Attribute
     */
    public function  description(): Attribute
    {
        return Attribute::make(
            get: fn($value)  => Str::title($value),
            set: fn($value)  => ucwords($value),
        );
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'event_id', 'id');
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }


    public function mainImage()
    {
        return $this->morphOne(Image::class, 'imageable')->ofMany('created_at', 'min');
    }
}
