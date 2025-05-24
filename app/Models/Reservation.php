<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $tabel = "reservations";

    protected $fillable = ['event_id', 'seats_reserved'];

    protected $guarded = ['user_id'];


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
