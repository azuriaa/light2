<?php

namespace App\Config;

use Light2\Router;
use App\Middlewares\ExampleMiddleware;
use App\Controllers\Home;

class Routes
{
    public static function register(): void
    {
        Router::add('/', function ($id = null) {
            Router::controller(
                Home::class,
                $id,
                ExampleMiddleware::class,
            );
        });
    }
}