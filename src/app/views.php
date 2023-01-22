<?php

declare(strict_types=1);

use DI\Container;
use Slim\Views\Twig;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

use App\Models\User;

class AssetExtension extends AbstractExtension
{
    function getFunctions()
    {
        return [
            new TwigFunction('getUser', [$this, 'getUser'])
        ];
    }

    function getUser()
    {
        if (isset($_SESSION['user'])) {
            return User::find($_SESSION['user']);
        }
    }
}

$new_asset = new AssetExtension;

return function (Container $container) use($new_asset) {
    $container->set('twig', function() use ($container, $new_asset) {
        $settings = $container->get('settings')['views'];
        $twig = Twig::create($settings['template_path'], ['cache' => false]);
        $twig->addExtension($new_asset);
        
        return $twig;
    });
};
