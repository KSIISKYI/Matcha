<?php

namespace App\Controllers;

use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Illuminate\Support\Collection;

use App\Models\{Chat, User, Profile};
use App\Service\ProfileService;

class HomeController extends Controller
{
    public function index(Request $request, Response $response)
    {
        $my_profile = Profile::find(1);
        // $matches = implode(' ,', $my_profile->interesting_profiles->intersect($my_profile->interested_profiles)->pluck('id')->reverse()->toArray());

        // $profile = Profile::selectRaw("*, (id in (:matches)) as is_match", ['matches' => $matches])->get()->toArray();

        $profiles_query = ProfileService::getProfiles($my_profile);









        // $profiles_query = Profile::selectRaw('*,
        //             calcFameRating(fame_rating) as fame_rating_percent,
        //             calcCrow(position_x, position_y, :my_position_x, :my_position_y) as distance,
        //             id in (:matches) as \'match\',
        //             id in (:liked) as liked',
        //         [
        //             'my_position_x' => $my_profile->position_x,
        //             'my_position_y' => $my_profile->position_y,
        //             'matches' => implode(' ,', $my_profile->interesting_profiles->intersect($my_profile->interested_profiles)->pluck('id')->reverse()->toArray()),
        //             'liked' =>  implode(' ,', $my_profile->interesting_profiles->pluck('id')->toArray()),
        //         ]
        //     )
        //     ->whereRaw('id <> 1');

        echo '<pre>';
        print_r($profiles_query->get()->toArray());
        echo '</pre>';



        return $response;
    }
}
