<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Interest extends Model
{
    protected $table = 'interests';

    protected $guarded = ['id'];
    public $timestamps = false;

    public function profiles()
    {
        return $this->belongsToMany(Profile::class);
    }
}
