<?php

class Router
{
    public static string $prefix = '';

    protected static array $routes = [];
    protected static string $route = '';
    protected static string $name = '';
    protected static string $id = '';

    public static function add(string $route, callable $callback): void
    {
        array_push(Router::$routes, Router::$prefix . $route);
        Router::$routes[Router::$prefix . $route] = $callback;
    }

    public static function activate(callable $notFoundHandler = null): void
    {
        Router::$route = strtok($_SERVER['REQUEST_URI'], '?');
        Router::$name = strtok(Router::$route, '@');
        Router::$id = strtok('@');

        if (key_exists(Router::$name, Router::$routes)) {
            Router::$routes[Router::$name](Router::$id);
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
    public static function fail(string $message = 'Bad Request'): void
    {
        http_response_code(400);
        header('Content-Type: application/json; charset=utf-8');
        echo Handler::message('fail', $message);
    }

    public static function failNotFound(): void
    {
        http_response_code(404);
        header('Content-Type: application/json; charset=utf-8');
        echo Handler::message('fail', 'Not Found');
    }

    public static function failForbidden(): void
    {
        http_response_code(403);
        header('Content-Type: application/json; charset=utf-8');
        echo Handler::message('fail', 'Forbidden');
    }

    public static function error(string $error = 'Internal Server Error'): void
    {
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
        echo Handler::message('error', $error);
    }

    public static function notImplemented(): void
    {
        http_response_code(501);
        header('Content-Type: application/json; charset=utf-8');
        echo Handler::message('error', 'Not Implemented');
    }

    public static function respond(array $body = ['message' => 'OK']): void
    {
        $handlerTime = new \DateTime();

        $body['status'] = 'success';
        $body['time'] = $handlerTime->getTimestamp();

        http_response_code(200);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($body);
    }

    public static function respondCreated(string $message = 'Created'): void
    {
        http_response_code(201);
        header('Content-Type: application/json; charset=utf-8');
        echo Handler::message('success', $message);
    }

    public static function message(string $status, string $message): string
    {
        $handlerTime = new \DateTime();

        return json_encode([
            'status'    => $status,
            'time'      => $handlerTime->getTimestamp(),
            'message'   => $message,
        ]);
    }
}
