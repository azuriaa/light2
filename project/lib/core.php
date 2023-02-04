<?php

function import(string $file): void
{
    require_once realpath(__DIR__ . '/../' . $file . '.php');
}

class Router
{
    static array $routes = [];
    static string $prefix = '';

    static function add(string $route, callable $callback): void
    {
        array_push(Router::$routes, Router::$prefix . $route);
        Router::$routes[Router::$prefix . $route] = $callback;
    }

    static function activate(callable $notFoundHandler = null): void
    {
        if (key_exists(strtok($_SERVER['REQUEST_URI'], '?'), Router::$routes)) {
            Router::$routes[strtok($_SERVER['REQUEST_URI'], '?')]();
        } else {
            if ($notFoundHandler != null) {
                $notFoundHandler();
            } else {
                Handler::failNotFound();
            }
        }
    }
}

class Handler
{
    static function fail(string $message = 'Bad Request'): void
    {
        http_response_code(400);
        header('Content-Type: application/json; charset=utf-8');
        echo Handler::message('fail', $message);
    }

    static function failNotFound(): void
    {
        http_response_code(404);
        header('Content-Type: application/json; charset=utf-8');
        echo Handler::message('fail', 'Not Found');
    }

    static function failForbidden(): void
    {
        http_response_code(403);
        header('Content-Type: application/json; charset=utf-8');
        echo Handler::message('fail', 'Forbidden');
    }

    static function error(string $error = 'Internal Server Error'): void
    {
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
        echo Handler::message('error', $error);
    }

    static function notImplemented(): void
    {
        http_response_code(501);
        header('Content-Type: application/json; charset=utf-8');
        echo Handler::message('error', 'Not Implemented');
    }

    static function respond(array $body = ['message' => 'OK']): void
    {
        $handlerTime = new \DateTime();

        $body['status'] = 'success';
        $body['time'] = $handlerTime->getTimestamp();

        http_response_code(200);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($body);
    }

    static function respondCreated(string $message = 'Created'): void
    {
        http_response_code(201);
        header('Content-Type: application/json; charset=utf-8');
        echo Handler::message('success', $message);
    }

    static function message(string $status, string $message): string
    {
        $handlerTime = new \DateTime();

        return json_encode([
            'status'    => $status,
            'time'      => $handlerTime->getTimestamp(),
            'message'   => $message,
        ]);
    }
}
