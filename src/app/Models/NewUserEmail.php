<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewUserEmail extends Model
{
    protected $table = 'new_user_emails';
    protected $primaryKey = 'token';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
