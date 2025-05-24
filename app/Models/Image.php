<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{

    protected  $tabel = 'images';
    protected $fillable = ['url', 'imageable_type', 'imageable_id'];


    public function imageable()
    {
        return $this->morphTo();
    }
}
