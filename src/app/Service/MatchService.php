<?php

namespace App\Service;

use DI\Container;

use App\Models\Profile;
use App\Service\Chat\ChatService;

class MatchService
{
    public static function markViewed(Container $container, $profile_id)
    {
        $profile = $container->get('user')->profile;

        $profile->reviewed_profiles()->attach(['reviewed' => $profile_id]);
    }

    public static function likeProfile(Container $container, $profile_id)
    {
        $my_profile = $container->get('user')->profile;
        $interest_profile = Profile::find($profile_id);

        $interest_profile->fame_rating += 5;
        $interest_profile->save();
        $my_profile->interesting_profiles()->attach(['interesting' => $profile_id]); 
    }

    public static function checkForMacth(Container $container, $profile_id)
    {
        $my_profile = $container->get('user')->profile;

        if (in_array($profile_id, $my_profile->interested_profiles->pluck('id')->toArray())) {
            $profile_2 = Profile::with('user', 'profile_photos')->find($profile_id);
            $chat = ChatService::createChat($my_profile, $profile_2);
            $profile_2->new_chat = $chat;

            return $profile_2->toArray();
        }

        return [];
    }
}
