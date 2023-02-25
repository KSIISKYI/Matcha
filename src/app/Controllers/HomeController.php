<?php

namespace App\Controllers;

use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class HomeController extends Controller
{
    public function index(Request $request, Response $response)
    {
        $view = Twig::fromRequest($request);

        return $view->render($response, 'home.twig');
    }
}
