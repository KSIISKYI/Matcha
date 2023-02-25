<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfilePhoto extends Model
{
    protected $table = 'profile_photos';

    protected $guarded = ['id'];
    public $timestamps = false;

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
