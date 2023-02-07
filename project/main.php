<?php

function main(): void
{
    Router::add('/', function (string $id = '') {
        if ($id == '') {
            header('Location: ' . Router::$prefix . '/@World!');
        }

        require_once 'example/hello_world.php';
    });

    Router::activate();
}
