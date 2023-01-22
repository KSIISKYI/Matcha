<?php

namespace App\Controllers;

use DI\Container;

class Controller
{
    protected $container;

    function __construct(Container $container)
    {
        $this->container = $container;
    }
}
