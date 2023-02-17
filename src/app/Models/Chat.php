<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $table = 'chats';
    // protected $attributes = ['last_message'];
    protected $appends = ['last_message'];
    protected $guarded = ['id'];

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function getLastMessageAttribute()
    {
        return Message::where('chat_id', '=', $this->id)->get()->last();
    }
}
