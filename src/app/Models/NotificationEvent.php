<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationEvent extends Model
{
    public $timestamps = false;
    protected $table = 'notification_events';
    protected $guarded = ['id'];
}
