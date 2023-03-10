<?php

declare(strict_types=1);

use Slim\App;
use Slim\Routing\RouteCollectorProxy;

use App\Middleware\{GuestMiddleware, AuthenticateMiddleware, IsProfilePhotoCreatorMiddleware, IsNativeUserMiddleware, IsMyProfileMiddleware, IsChatParticipantMiddleware};

return function(App $app) {
    $app->group('', function(RouteCollectorProxy $group) {
        $group->get('/auth/signup', 'RegisterController:show_form')->setName('signup-get');
        $group->post('/auth/signup', 'RegisterController:register')->setName('signup-post');
        $group->get('/auth/google', 'GoogleAuthController:authorize');
        $group->post('/auth/activate', 'RegisterController:activate');
        $group->get('/auth/signin', 'AuthController:show_form')->setName('signin-get');
        $group->post('/auth/signin', 'AuthController:login')->setName('signin-post');
    })->add(new GuestMiddleware($app->getContainer()));

    $app->group('', function(RouteCollectorProxy $group) use($app) {
        $group->get('/profiles/my', 'ProfileController:showMe')->setName('profile-index');
        $group->get('/profiles/{profile_id}', 'ProfileController:show')->setName('profile-show')->add(new IsMyProfileMiddleware($app->getContainer()));
        $group->get('/profiles', 'ProfileController:getProfiles');
        $group->get('/auth/signout', 'AuthController:logout')->setName('signout-get');

        $group->get('/profile/settings', 'ProfileController:showSettings')->setName('profile_settings-get');
        $group->post('/profile/settings', 'ProfileController:update');

        $group->post('/profile/profile_images', 'ProfilePhotoController:store');
        $group->delete('/profile/profile_images/{profile_image_id}', 'ProfilePhotoController:destroy')
            ->add(new IsProfilePhotoCreatorMiddleware($app->getContainer()));

        $group->get('/discovery_settings', 'DiscoverySettingsController:showSettings')->setName('discovery_settings-get')->add('csrf');
        $group->post('/discovery_settings', 'DiscoverySettingsController:updateSettings')->setName('discovery_settings-post')->add('csrf');

        $group->get('/find_match', 'MatchController:index')->setName('match-index');
        $group->post('/find_match/{profile_id}', 'MatchController:checkForMatch');
        $group->get('/my_matches', 'MatchController:getMyMatches')->setName('my_matches-get');

        $group->get('/activity_log', 'ProfileController:getActivityLog')->setName('activity_log-get');

        $group->get('/block_profile/{profile_id}', 'ProfileController:blockProfile')->setName('block_profile');
        $group->get('/unblock_profile/{profile_id}', 'ProfileController:unblockProfile')->setName('unblock_profile');
        $group->get('/report_fake_profile/{profile_id}', 'ProfileController:reportFakeProfile')->setName('report_fake_profile');

        $group->get('/chats', 'ChatController:index')->setName('chats-index');
        $group->get('/chats/{chat_id}', 'ChatController:show')->setName('chats-show')->add(new IsChatParticipantMiddleware($app->getContainer()));

        $group->get('/chats/{chat_id}/messages', 'MessageController:index')->setName('messages-index')->add(new IsChatParticipantMiddleware($app->getContainer()));

        $group->get('/notifications', 'NotificationController:index')->setName('notifications-index');
        $group->get('/notifications/{notification_id}', 'NotificationController:show')->setName('notifications-show');
    })->add(new AuthenticateMiddleware($app->getContainer())); 

    $app->group('', function(RouteCollectorProxy $group) use($app) {
        $group->get('/account_settings', 'UserController:showSettings')->setName('account_settings-get')->add('csrf');
        $group->post('/account_settings', 'UserController:updateSettings')->setName('account_settings-post')->add('csrf');
    })->add(new IsNativeUserMiddleware($app->getContainer()))->add(new AuthenticateMiddleware($app->getContainer()));

    $app->get('', function() {})->setName('base_path');
    $app->get('/', 'HomeController:index')->setName('home');
    $app->post('/account_settings/reset_password', 'UserController:resetPassword')->setName('reset_password-post');
    $app->get('/change_email', 'UserController:sendActivationMail');
    $app->post('/change_email', 'UserController:changeMail');
    $app->get('/test', 'HomeController:index');
};
