<?php

declare(strict_types=1);

use Rakit\Validation\Validator;
use DI\Container;

use App\Validation\Rules\{EmailAvailable, UsernameAvailable, CheckOldPassword};

return function(Container $container) {
    $container->set('validator', function() use ($container) {
        $validator = new Validator;

        $validator->addValidator('emailAvailable', new EmailAvailable);
        $validator->addValidator('usernameAvailable', new UsernameAvailable);
        $validator->addValidator('checkOldPassword', new CheckOldPassword);

        return $validator;
    });
};
