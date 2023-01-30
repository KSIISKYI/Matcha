<?php

namespace App\Controllers\Auth;

use Slim\Views\Twig;
use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Controllers\Controller;
use App\Service\{UserService, GoogleAuthService};

class RegisterController extends Controller
{
    function show_form(Request $request, Response $response)
    {
        $view = Twig::fromRequest($request);
        $context = $this->container->get('flash')->getMessages();
        $context['google_code'] = GoogleAuthService::getAuthUrl();
        
        return $view->render($response, 'auth/signup.twig', $context);
    }

    function register(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $validator = $this->container->get('validator');
        $flash = $this->container->get('flash');
        $errors = UserService::validateRegisterForm($validator, $data);

        if ($errors) {
            $flash->addMessage('data', $data);
            $flash->addMessage('errors', $errors);
        } else {
            UserService::registerUser($data);
            $flash->addMessage('message', 'Registration is successful, go to your email and confirm it');
        }
    
        return $response->withStatus(302)->withHeader('Location', $this->container->get('router')->urlFor('signup-get'));
    }

    function activate(Request $request, Response $response)
    {
        $data = $request->getParsedBody();

        if (!UserService::activateUser($data)) {
            $response->getBody()->write('Activation token is invalid');

            return $response->withStatus(404);
        }
    }
}
