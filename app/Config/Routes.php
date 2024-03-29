<?php

namespace App\Config;

use Light2\Services\Router;
use App\Controllers\Home;

class Routes
{
    public static function register(): void
    {
        Router::add('/', function () {
            Router::controller(Home::class);
        });
    }
}