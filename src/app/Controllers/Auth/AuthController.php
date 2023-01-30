<?php

namespace App\Controllers\Auth;

use Slim\Views\Twig;
use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Controllers\Controller;
use App\Service\{UserService, GoogleAuthService};

class AuthController extends Controller
{
    public function show_form(Request $request, Response $response)
    {
        $view = Twig::fromRequest($request);
        $context = $this->container->get('flash')->getMessages();
        $context['google_code'] = GoogleAuthService::getAuthUrl();
        
        return $view->render($response, 'auth/signin.twig', $context);
    }

    public function login(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $flash = $this->container->get('flash');
        $res = UserService::validateLoginForm($data);

        if (!$res['error']) {
            UserService::loginUser($res['user']);
            return $response->withStatus(302)->withHeader('Location', $this->container->get('router')->urlFor('profile-index'));
        } 

        $flash->addMessage('message', $res['error']);
        return $response->withStatus(302)->withHeader('Location', $this->container->get('router')->urlFor('signin-get'));
    }

    public function logout(Request $request, Response $response)
    {
        $_SESSION = [];

        return $response->withStatus(302)->withHeader('Location', $this->container->get('router')->urlFor('signin-get'));
    }
}
