<?php

namespace App\Validation\Rules;

use Rakit\Validation\Rule;

use App\Models\User;

class UsernameAvailable extends Rule
{
    protected $message = "Username \":value\" is already taken";

    public function check($value): bool
    {
        return !User::where('username', $value)->count();
    }
}
