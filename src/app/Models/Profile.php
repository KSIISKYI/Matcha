<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $table = 'profiles';

    protected $guarded = ['id'];
    public $timestamps = false;
    protected $appends = ['count_unreviewed_messages'];
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

    public function activity_log()
    {
        return $this->belongsToMany(Profile::class, 'activity_log', 'viewer', 'reviewed')->orderByPivot('created_at', 'desc')->withTimestamps();
    }

    public function blocked_profiles()
    {
        return $this->belongsToMany(Profile::class, 'blocked_profiles', 'blocker', 'blocked');
    }

    public function blockers()
    {
        return $this->belongsToMany(Profile::class, 'blocked_profiles', 'blocked', 'blocker');
    }

    public function fake_profiles()
    {
        return $this->belongsToMany(Profile::class, 'fake_profile_reports', 'reporter', 'fake_profile');
    }

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    public function getCountUnreviewedMessagesAttribute()
    {
        $my_profile = Profile::find($this->id);
        $res = 0;

        foreach($my_profile->participants as $participant) {
            $chat = $participant->chat;
            $res += $chat->messages->where('reviewed', false)->where('participant_id', '!=' ,$participant->id)->count();
        }

        return $res;
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'notified_id', 'id');
    }
}
