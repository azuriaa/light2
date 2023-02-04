<?php

class Application
{
    public static function main(): void
    {
        Router::add('/', function () {
            require_once('example/hello_world.php');
        });

        Router::activate();
    }
}
