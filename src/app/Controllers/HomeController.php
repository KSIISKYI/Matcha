<?php

namespace App\Controllers;

use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Models\{User, Profile};

class HomeController extends Controller
{
    public function index(Request $request, Response $response)
    {
        return $response;
    }
}
