<?php
namespace App\Service;

use DI\Container;

use App\Models\{Profile, User, DiscoverySetting};

class ProfileService
{
    public static function createProfile(User $user)
    {
        $discovery_settings = DiscoverySetting::create();
        $profile = Profile::create(['user_id' => $user->id, 'discovery_settings_id' => $discovery_settings->id]);

        return $profile;
    }

    public static function validateProfileForm(Container $container, array $data)
    {
        $validator = $container->get('validator');

        $validation = $validator->make($data, [
            'username' => $container->get('user')->username === $data['username'] ? '' : 'required|min:6|max:15|usernameAvailable|alpha_dash',
            'first_name' => 'max:15|alpha_spaces',
            'last_name' => 'max:15|alpha_spaces',
            'about_me' => 'max:250',
            'age' => 'numeric|min:18|max:80',
            'gender' => '',
            'interests' => '',
            'instagram_link' => '',
            'twitter_link' => '',
            'facebook_link' => '',
        ]);

        $validation->validate();

        if ($validation->fails()) {
            return $validation->errors()->toArray();
        }
    }

    public static function updateProfile(Container $container, array $data)
    {
        $profile = $container->get('user')->profile;
        
        $profile->update([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'about_me' => $data['about_me'],
            'age' => empty($data['age']) ? null : $data['age'],
            'gender_id' => empty($data['gender']) ? null : $data['gender'],
            'instagram_link' => empty($data['instagram_link']) ? null : $data['instagram_link'],
            'twitter_link' => empty($data['twitter_link']) ? null : $data['twitter_link'],
            'facebook_link' => empty($data['facebook_link']) ? null : $data['facebook_link']
        ]);

        $profile->interests()->sync($data['interests']);
    }
}
