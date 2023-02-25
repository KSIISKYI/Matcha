<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';
    protected $guarded = ['id'];
    protected $with = array('event');

    public function notifier()
    {
        return $this->belongsTo(Profile::class, 'notifier_id');
    }

    public function notified()
    {
        return $this->belongsTo(Profile::class, 'notified_id');
    }

    public function event()
    {
        return $this->belongsTo(NotificationEvent::class, 'event_id');
    }
}
