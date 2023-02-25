<?php
namespace App\Service;

use DI\Container;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;


use App\Models\{Profile, User, DiscoverySetting, ReviewedProfile};
use App\Service\Chat\ChatService;


class ProfileService
{
    public static function createProfile(User $user)
    {
        $discovery_settings = DiscoverySetting::create();
        $profile = Profile::create([
            'user_id' => $user->id, 
            'discovery_settings_id' => $discovery_settings->id, 
            'position_x' => 50.450001,
            'position_y' => 31.523333
        ]);

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

    public static function getMatchProfiles(Container $container)
    {
        $my_profile = $container->get('user')->profile;
        $profile_ids = $my_profile->interesting_profiles->intersect($my_profile->interested_profiles)->pluck('id')->reverse()->toArray();
        $res = new Collection;

        foreach($profile_ids as $profile_id) {
            $res->push(self::getProfile($container, $profile_id));
        }

        return $res;
    }

    public static function getActivityLog(Container $container)
    {
        $my_profile = $container->get('user')->profile;
        $profile_ids = $my_profile->activity_log->pluck('id')->toArray();
        $res = new Collection;

        foreach($profile_ids as $profile_id) {
            $res->push(self::getProfile($container, $profile_id));
        }

        return $res;
    }

    public static function getProfile(Container $container, $profile_id)
    {
        $my_profile = $container->get('user')->profile;

        $position_x = $my_profile->position_x ? $my_profile->position_x : 0;
        $position_y = $my_profile->position_y ? $my_profile->position_y : 0;
        $matches = implode(', ', !empty($matches = $my_profile->interesting_profiles->intersect($my_profile->interested_profiles)->pluck('id')->reverse()->toArray()) ? $matches : [-1]);
        $liked = implode(', ', !empty($liked = $my_profile->interesting_profiles->pluck('id')->toArray()) ? $liked : [-1]);
        $blocked_profiles = implode(', ', !empty($blocked_profiles = $my_profile->blocked_profiles->pluck('id')->toArray()) ? $blocked_profiles : [-1]);
        $blockers = implode(', ', !empty($blockers = $my_profile->blockers->pluck('id')->toArray()) ? $blockers : [-1]);
        $fake_profiles = implode(', ', !empty($fake_profiles = $my_profile->fake_profiles->pluck('id')->toArray()) ? $fake_profiles : [-1]);

        $profile_query = Profile::selectRaw("*,
                    calcFameRating(fame_rating) as fame_rating_percent, 
                    calcCrow(position_x, position_y, $position_x, $position_y) as distance,
                    id in ($matches) as 'match',
                    id in ($liked) as liked,
                    id in ($blocked_profiles) as is_blocked,
                    id in ($blockers) as is_blocker,
                    id in ($fake_profiles) as is_reported",
            )
            ->whereIn('id', [$profile_id]);

        return $profile_query->first();
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
            ->whereNotIn('p.id', $my_profile->blockers->merge($my_profile->blocked_profiles)->pluck('id'))
            ->whereNotIn('p.id', $my_profile->fake_profiles->pluck('id'))
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
        
        $suitable_profiles = $suitable_profiles->orderByRaw('distance, count(*) desc, fame_rating_percent desc')->get();

        return $suitable_profiles;
    }

    public static function addToActivityLog(Container $container, $profile_id)
    {
        $my_profile = $container->get('user')->profile;
        $last_activity = $my_profile->activity_log->where('id', '=', $profile_id)->first();

        $now = Carbon::now()->subHour();

        if (!$last_activity || $now->gt($last_activity->pivot->created_at)) {
            $my_profile->activity_log()->attach($profile_id);
        }
    }

    public static function blockProfile(Container $container, $data)
    {
        $my_profile = $container->get('user')->profile;
        $chat = ChatService::getChats($my_profile)->intersect(ChatService::getChats(Profile::find($data['profile_id'])))->first();

        try { 
            $my_profile->blocked_profiles()->syncWithoutDetaching(['blocked' => $data['profile_id']]);
            $my_profile->interesting_profiles()->detach(['blocked' => $data['profile_id']]);

            if ($chat) {
                $chat->delete();
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            return false;
        }

        return true;
    }

    public static function unblockProfile(Container $container, $data)
    {
        $my_profile = $container->get('user')->profile;

        try { 
            $my_profile->blocked_profiles()->detach(['blocked' => $data['profile_id']]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return false;
        }

        return true;
    }

    public static function reportFakeProfile(Container $container, $data)
    {
        $my_profile = $container->get('user')->profile;

        try { 
            $my_profile->fake_profiles()->syncWithoutDetaching(['fake_profile' => $data['profile_id']]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return false;
        }

        return true;
    }
}
