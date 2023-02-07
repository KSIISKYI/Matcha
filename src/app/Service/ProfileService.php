<?php
namespace App\Service;

use DI\Container;
use Illuminate\Database\Capsule\Manager as Capsule;

use App\Models\{Profile, User, DiscoverySetting, ReviewedProfile};

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

    public static function getProfileWithPopularity(Container $container, $profile_id)
    {
        $my_profile = $container->get('user')->profile;
        $profile = Profile::selectRaw('*, 
                calcFameRating(fame_rating) as fame_rating_percent, 
                calcCrow(position_x, position_y, ?, ?) as distance',
            [$my_profile->position_x, $my_profile->position_y]
        )->find($profile_id);

        return $profile;
    }

    public static function getSuitableProfiles(Container $container)
    {
        $user = $container->get('user');
        $my_profile = $user->profile;

        $suitable_profiles = Profile::from('profiles as p')
            ->selectRaw("
                p.*, 
                calcCrow(position_x, position_y, ?, ?) as distance,
                calcFameRating(fame_rating) as fame_rating_percent",
                [$my_profile->position_x, $my_profile->position_y]
            )
            ->leftJoin('matcha.interest_profile as i_p', 'p.id', '=', 'i_p.profile_id')
            ->whereNotIn('user_id', [$user->id])
            ->whereRaw('
                (select max_distance from matcha.profiles AS pp
                join matcha.discovery_settings as d on (pp.discovery_settings_id = d.id)
                where pp.id = ?) > (select calcCrow(p.position_x, p.position_y, ppp.position_x, ppp.position_y) from matcha.profiles as ppp where ppp.id = 1)',
                [$my_profile->id]
            )
            ->whereRaw('calcFameRating(fame_rating) between ? and ?', [
                    $my_profile->discovery_settings->fame_rating_min, 
                    $my_profile->discovery_settings->fame_rating_max
                ]
            )
            ->whereBetween('age', [$my_profile->discovery_settings->age_min, $my_profile->discovery_settings->age_max])
            ->whereNotIn('p.id', $my_profile->reviewed_profiles->pluck('id'))
            ->whereNotIn('p.id', $my_profile->interesting_profiles->pluck('id'))
            ->groupBy('p.user_id');

        if ($interests = $my_profile->discovery_settings->interests->pluck('id')->toArray()) {
            $suitable_profiles = $suitable_profiles->whereIn('i_p.interest_id', $interests); 
        }

        if (!is_null($gender = $my_profile->discovery_settings->gender_id)) {
            $suitable_profiles = $suitable_profiles->whereRaw('(gender_id = ? or gender_id is null)', [$gender]);
        }
        
        $suitable_profiles = $suitable_profiles->orderByRaw('distance, count(*) desc, fame_rating_percent desc')
            ->take(10)
            ->get()
            ->toArray();

        return $suitable_profiles;
    }
}
