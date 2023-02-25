<?php

declare(strict_types=1);

use DI\Container;
use Slim\Views\Twig;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

use App\Models\Profile;

class AssetExtension extends AbstractExtension
{
    function getFunctions()
    {
        return [
            new TwigFunction('getProfile', [$this, 'getProfile'])
        ];
    }

    function getProfile()
    {
        if (isset($_SESSION['user'])) {
            $profile =  Profile::where('user_id', $_SESSION['user'])
                ->withCount(['notifications as count_unreviewed_notifications' => function($query) {
                    $query->where('reviewed', false);
                }])
                ->first();

            return $profile->toArray();
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
