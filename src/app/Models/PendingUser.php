<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendingUser extends Model
{
    protected $table = 'pending_users';
    protected $primaryKey = 'token';
    protected $guarded = [];
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
