<?php

namespace App\Controllers;

use Slim\Views\Twig;
use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Models\{Gender, Interest};
use App\Service\DiscoverySettingsService;

class DiscoverySettingsController extends Controller
{
    function showSettings(Request $request, Response $response)
    {
        $view = Twig::fromRequest($request);
        $user = $this->container->get('user');

        $csrf = $this->container->get('csrf');
        $nameKey = $csrf->getTokenNameKey();
        $valueKey = $csrf->getTokenValueKey();
        $name = $request->getAttribute($nameKey);
        $value = $request->getAttribute($valueKey);

        $tokenArray = [
            $nameKey => $name,
            $valueKey => $value
        ];

        $context = [
            'user' => $user,
            'discovery_settings' => $user->profile->discovery_settings,
            'genders' => Gender::all(),
            'interests' => Interest::all()
        ];

        return $view->render($response, 'discovery_settings.twig', array_merge($context, ['csrf_tokens' => $tokenArray]));
    }

    function updateSettings(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        DiscoverySettingsService::update($this->container, $data);

        return $response;
    }
}
