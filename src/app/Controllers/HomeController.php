<?php

namespace App\Controllers;

use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Models\{User, Profile};

class HomeController extends Controller
{
    public function index(Request $request, Response $response)
    {
        $profile = Profile::find(1);

        // var_dump($profile2);

        // // var_dump($profile);

        // $profile->reviewed_profiles()->attach(['reviewed' => 2]);
        // $profile->test = true;
        // $profile->blocked_profiles()->syncWithoutDetaching(['blocked' => 3]);

        echo '<pre>';
        print_r($profile->fake_profiles->toArray());
        // print_r($profile->blockers->merge($profile->blocked_profiles)->toArray());
        echo '</pre>';



        // return $response;
    }
}
