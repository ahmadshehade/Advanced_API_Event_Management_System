<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $tabel = "reservations";

    protected $fillable = ['event_id', 'seats_reserved','status','confirmed_at'];

    protected $guarded = ['user_id'];


    public function scopeWithRelations($query){
        $query->with(['event','user']);
    }

    public function scopeReservationNotCancelled($query)
    {
        return $query->where('status', '!=', 'cancelled');
    }

    /**
     * Summary of user
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, Reservation>
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    /**
     * Summary of event
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Event, Reservation>
     */
    public function event(){
        return $this->belongsTo(Event::class,'event_id','id');
    }
}
