<?php

namespace App\Service;

use Google\Client;
use Google\Service\Oauth2;

use App\Models\User;

class GoogleAuthService
{
    private static function getClient()
    {
        $client = new Client();
        $client->setClientId ($_ENV['CLIENT_ID']);
        $client->setClientSecret ($_ENV['CLIENT_SECRET']);
        $client->setApplicationName($_ENV['APPLICATION_NAME']);
        $client -> setRedirectUri ($_ENV['REDIRECT_URL']);
        $client -> addScope ($_ENV['SCOPE']);

        return $client;
    }

    public static function getAuthUrl()
    {
        $client = self::getClient();

        return $client->createAuthUrl();
    }

    public static function getClientData(array $data)
    {
        $client = self::getClient();

        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
	    $oAuth = new Oauth2($client);
	    $user_data = $oAuth->userinfo->get();

        return $user_data;
    }

    public static function authorize($user_data)
    {
        $user = User::where('email', $user_data['email'])->first();

        if ($user && $user->is_google_auth) {
            UserService::loginUser($user);
        } elseif ($user) {
            return ['error' => 'user with this data doesn\'t exists']; 
        } else {
            $user = self::createUser($user_data);
            UserService::loginUser($user);
        }
    }

    public static function createUser($user_data) {
        $user = User::create([
            'email' => $user_data->email,
            'username' => self::generateRandomString(),
            'is_active' => true,
            'is_google_auth' => true
        ]);

        $profile = ProfileService::createProfile($user);
        $profile->first_name = $user_data['givenName'];
        $profile->last_name = $user_data['familyName'];
        $profile->save();

        mkdir(__DIR__ . '/../../public/img/profile_images/' . $profile->id, 0777);
        
        return $user;
    }

    public static function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[mt_rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
