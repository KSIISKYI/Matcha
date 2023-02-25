<?php

namespace App\Service\Chat;

use DI\Container;

use App\Models\{Chat, Profile};
use App\Service\ProfileService;
use Illuminate\Database\Eloquent\Collection;

class ChatService
{
    public static function createChat(Profile $profile_1, Profile $profile_2)
    {
        $chat = Chat::create();
        self::createParticipant($profile_1, $chat);
        self::createParticipant($profile_2, $chat);

        return $chat;
    }

    public static function createParticipant(Profile $profile, Chat $chat)
    {
        $participant = $chat->participants()->create(['profile_id' => $profile->id]);
        
        return $participant;
    }

    public static function getChats(Profile $profile)
    {
        $chats = new Collection;
        
        foreach($profile->participants as $participant) {
            $chat = $participant->chat;

            $chat->other_participant = ($other = $chat->participants->first())->id == $participant->id ? 
                $chat->participants->last()->load('profile')->toArray() : $other->load('profile')->toArray();
            $chats->push($chat);
        } 

        return $chats->sortBy('updated_at')->reverse();
    }
}
