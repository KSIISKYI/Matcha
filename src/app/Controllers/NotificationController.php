<?php

namespace App\Controllers;

use Slim\Views\Twig;
use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Models\Notification;

class NotificationController extends Controller
{
    public function index(Request $request, Response $response)
    {
        $data = $request->getQueryParams();
        $view = Twig::fromRequest($request);
        $profile = $this->container->get('user')->profile;
        $notifications = $profile->notifications()
            ->orderBy('id', 'desc')
            ->with('notifier')
            ->paginate(5, ['*'], 'page', isset($data['page']) ? $data['page'] : 1)
            ->withPath($this->container->get('router')->urlFor('notifications-index'));

        return $view->render($response, 'notifications.twig', $notifications->toArray());
    }
    
    public function show(Request $request, Response $response, $args)
    {
        $profile = $this->container->get('user')->profile;
        $notification = $profile->notifications->where('id', $args['notification_id'])->first();

        if ($notification) {
            $notification->reviewed = true;
            $notification->save();
        }

        return $response;
    }
}
