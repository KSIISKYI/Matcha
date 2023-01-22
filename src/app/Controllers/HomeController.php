<?php

namespace App\Controllers;

use Slim\Views\Twig;
use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Rakit\Validation\Validator;

use App\Models\{User, PendingUser};

class HomeController extends Controller
{
    function index(Request $request, Response $response)
    {
        
        var_dump('HOME');

        // UserService::validateRegisterForm(['password' => 'usersfdfd1', 'c_password' => 'user2']);
        return $response;
    }
}
