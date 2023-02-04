<?php

class Application
{
    public static function main(): void
    {
        Router::add('/', function () {
            import('pages/home');
        });

        Router::activate();
    }
}
