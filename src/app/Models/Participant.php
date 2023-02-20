<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    protected $table = 'participants';
    protected $with = array('chat');
    protected $guarded = ['id'];
    public $timestamps = false;

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
