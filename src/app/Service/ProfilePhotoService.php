<?php

namespace App\Service;

use Psr\Http\Message\ServerRequestInterface as Request;
use DI\Container;

use App\Models\ProfilePhoto;

class ProfilePhotoService
{
    public static function savePhoto(Request $request)
    {
        $img_data = base64_decode($request->getParsedBody()['img_base64']);
        $img_name = 'profile_images/' . time() . random_int(10, 999) . '.jpeg';
        file_put_contents(__DIR__ . '/../../public/img/' . $img_name, $img_data);

        return $img_name;
    }

    public static function create(Request $request, Container $container)
    {
        $img_name = self::savePhoto($request);

        $profile = $container->get('user')->profile;
        $profile->profile_photos()->create([
            'path' => $img_name,
            'profile' => $profile->id
        ]);
    }

    public static function remove($profile_photo_id)
    {
        $profile_photo = ProfilePhoto::find($profile_photo_id);
        $img_path = $profile_photo->path;

        unlink(__DIR__ . '/../../public/img/' . $img_path);
        $profile_photo->delete();
    }
}
