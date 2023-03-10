<?php

namespace App\Controllers;

use Slim\Views\Twig;
use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Service\{MailService, UserService};

class UserController extends Controller
{
    public function showSettings(Request $request, Response $response)
    {
        $view = Twig::fromRequest($request);

        $csrf = $this->container->get('csrf');
        $nameKey = $csrf->getTokenNameKey();
        $valueKey = $csrf->getTokenValueKey();
        $name = $request->getAttribute($nameKey);
        $value = $request->getAttribute($valueKey);
        $context = $this->container->get('flash')->getMessages();

        $tokenArray = [
            $nameKey => $name,
            $valueKey => $value
        ];

        return $view->render($response, 'account_settings.twig', [
            'csrf_tokens' => $tokenArray,
            'context' => $context
        ]);
    }

    public function updateSettings(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $validator = $this->container->get('validator');
        $flash = $this->container->get('flash');

        $errors = UserService::validateAccountSettingsForm($validator, $this->container, $data);

        if ($errors) {
            $flash->addMessage('data', $data);
            $flash->addMessage('errors', $errors);
        } else {
            if ($this->container->get('user')->email !== $data['email']) {
                $new_user_email = UserService::createNewEmailUser($this->container->get('user'), $data['email']);
                MailService::sendChangeMail($this->container->get('user'), $new_user_email);
                $flash->addMessage('messages', 'We sent form email activation on your new email.');
            } 
            if (!empty($data['old_password']) || !empty($data['new_password']) || !empty($data['confirm_new_password'])) {
                $flash->addMessage('messages', 'Password successfully changed.');
                UserService::changePassword($this->container->get('user'), $data['new_password']);
            } 
        }

        return $response->withStatus(302)->withHeader('Location', $this->container->get('router')->urlFor('account_settings-get'));
    }

    public function resetPassword(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        UserService::resetPassword($data);

        return $response; 
    }

    function changeMail(Request $request, Response $response)
    {
        $flash = $this->container->get('flash');

        if(UserService::changeEmail($request->getParsedBody())) {
            $_SESSION = [];
            $flash->addMessage('message', 'Email changed successfully');
            return $response->withStatus(302)->withHeader('Location', $this->container->get('router')->urlFor('signin-get'));
        }

        return $response->withStatus(400,'Bad Request');
    }
}
