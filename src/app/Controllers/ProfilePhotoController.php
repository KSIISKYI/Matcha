<?php

namespace App\Controllers;

use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Service\ProfilePhotoService;

class ProfilePhotoController extends Controller
{
    public function store(Request $request, Response $response)
    {
        ProfilePhotoService::create($request, $this->container);

        return $response;
    }

    public function destroy(Request $request, Response $response, $args)
    {
        ProfilePhotoService::remove($args['profile_image_id']);

        return $response;
    }
}
