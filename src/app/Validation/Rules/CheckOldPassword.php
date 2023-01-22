<?php

namespace App\Validation\Rules;

use Rakit\Validation\Rule;

use App\Models\User;

class CheckOldPassword extends Rule
{
    protected $message = "The password is entered incorrectly";

    public function check($value): bool
    {
        $user = User::find($_SESSION['user']);
        
        return password_verify($value, $user->password);
    }
}
