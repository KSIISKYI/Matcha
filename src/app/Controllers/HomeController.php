<?php

namespace App\Controllers;

use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Models\{User, Profile};

class HomeController extends Controller
{
    public function index(Request $request, Response $response)
    {
        $profile = Profile::all()->first();
        // // var_dump($profile);

        // $profile->reviewed_profiles()->attach(['reviewed' => 2]);

        echo '<pre>';
        print_r($profile->interesting_profiles->toArray());
        echo '</pre>';



        // return $response;
    }
}
