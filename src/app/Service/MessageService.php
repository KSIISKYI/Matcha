<?php

namespace App\Service;

use App\Models\{Chat, Message};

class MessageService
{
    public static function getMessages($container, $args, $page)
    {
        $my_profile = $container->get('user')->profile;
        $messages = Chat::find($args['chat_id'])->messages()->orderBy('id', 'desc')->paginate(7, ['*'], 'page', $page);
        $messages->flatMap(function($el) use($my_profile) {
            if (!in_array($el->participant_id, $my_profile->participants->pluck('id')->toArray())) {
                $el->timestamps = false;
                $el->reviewed = true;
                $el->save();
            }
        });
        $data = $messages->withPath($container->get('router')->urlFor('messages-index', ['chat_id' => $args['chat_id']]))->toArray();

        return $data;
    }

    private static function getMessage($message_id, $chat_id, $participant)
    {
        $message = Message::where('chat_id', $chat_id)
            ->where('participant_id', $participant)
            ->find($message_id);
        
        return $message;
    }

    public static function updateMessage($message_id, $chat_id, $new_context, $participant)
    {
        $message = self::getMessage($message_id, $chat_id, $participant);

        if ($message) {
            $message->update(['message' => $new_context]);
        }

        return $message;
    }

    public static function removeMessage($message_id, $chat_id, $participant)
    {
        $message = self::getMessage($message_id, $chat_id, $participant);

        if ($message) {
            $message->delete();
        }
    }
}