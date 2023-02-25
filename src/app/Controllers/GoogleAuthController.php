<?php

namespace App\Controllers;

use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Service\GoogleAuthService;

class GoogleAuthController extends Controller
{
    public function authorize(Request $request, Response $response)
    {   
        $flash = $this->container->get('flash');
        $data = $request->getQueryParams();
        $user_data = GoogleAuthService::getClientData($data);
        $errors = GoogleAuthService::authorize($user_data);
        
        if ($errors) {
            $flash->addMessage('message', $errors['error']);

            return $response->withStatus(302)->withHeader('Location', $this->container->get('router')->urlFor('signin-get'));
        }

        return $response->withStatus(302)->withHeader('Location', $this->container->get('router')->urlFor('profile-index'));
    }
}
