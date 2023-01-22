<?php

namespace App\Service;

use App\Models\{Profile, User};

class ProfileService
{
    static function createProfile(User $user)
    {
        $profile = Profile::create(['user_id' => $user->id]);

        return $profile;
    }
}
