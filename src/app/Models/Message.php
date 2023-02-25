<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'messages';
    protected $guarded = ['id'];

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }
}
