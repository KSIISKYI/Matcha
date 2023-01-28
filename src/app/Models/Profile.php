<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $table = 'profiles';

    protected $guarded = ['id'];
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function gender()
    {
        return $this->belongsTo(Gender::class);
    }

    public function profile_photos()
    {
        return $this->hasMany(ProfilePhoto::class);
    }

    public function interests()
    {
        return $this->belongsToMany(Interest::class);
    }

    public function discovery_settings()
    {
        return $this->belongsTo(DiscoverySetting::class);
    }
}