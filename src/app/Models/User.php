<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';

    protected $guarded = ['id'];
    public $timestamps = false;

    public function pending_user()
    {
        return $this->hasOne(PendingUser::class);
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }
}
