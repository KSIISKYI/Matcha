<?php

namespace App\Middleware;

use DI\Container;

class Middleware
{
    protected $container;

    function __construct(Container $container)
    {
        $this->container = $container;
    }
}
