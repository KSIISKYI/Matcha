<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $table = 'profiles';

    protected $guarded = ['id'];
    public $timestamps = false;
    protected $with = array('user', 'profile_photos');

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

    public function reviewed_profiles()
    {
        return $this->belongsToMany(Profile::class, 'reviewed_profiles', 'viewer', 'reviewed')
            ->withTimestamps()
            ->wherePivot('created_at', '>', date("Y-m-d H:i:s", time() - 86400));
    }

    public function viewers()
    {
        return $this->belongsToMany(Profile::class, 'reviewed_profiles', 'reviewed', 'viewer')->withTimestamps();
    }

    public function interested_profiles()
    {
        return $this->belongsToMany(Profile::class, 'match_profiles', 'interesting', 'interested');
    }

    public function interesting_profiles()
    {
        return $this->belongsToMany(Profile::class, 'match_profiles', 'interested', 'interesting');
    }
}
