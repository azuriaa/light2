<?php

namespace Light2;

use Light2\Services\RequestService;

class Router
{
    protected static array $routeCollection = [];
    protected static $notFoundHandler = null;

    public static function run(): void
    {
        $currentURI = explode(
            '/',
            service(RequestService::class)->getRequestTarget(),
        );

        $currentRoute = '/' . $currentURI[1];
        unset($currentURI[0], $currentURI[1]);

        if (key_exists($currentRoute, Router::$routeCollection)) {
            call_user_func_array(Router::$routeCollection[$currentRoute], $currentURI);
        } else {
            Router::runNotFoundHandler();
        }
    }

    public static function add(string $route, callable $callback): void
    {
        array_push(Router::$routeCollection, $route);
        Router::$routeCollection[$route] = $callback;
    }

    public static function controller($controller, $id = null, $middleware = null): void
    {
        $controller = service($controller);
        $method = service(RequestService::class)->getMethod();

        if (isset($middleware) && method_exists($middleware, 'before')) {
            $middleware::before();
        }

        if ($method == 'GET' && is_null($id) && method_exists($controller, 'index')) {
            $controller->index();
        } elseif ($method == 'GET' && isset($id) && method_exists($controller, 'show')) {
            $controller->show($id);
        } elseif ($method == 'POST' && method_exists($controller, 'create')) {
            $controller->create();
        } elseif (
            $method == 'PUT' && method_exists($controller, 'update') ||
            $method == 'PATCH' && method_exists($controller, 'update')
        ) {
            $controller->update($id);
        } elseif ($method == 'DELETE' && method_exists($controller, 'delete')) {
            $controller->delete($id);
        } else {
            Router::runNotFoundHandler();
        }

        if (isset($middleware) && method_exists($middleware, 'after')) {
            $middleware::after();
        }
    }

    public static function setNotFoundHandler(callable $handler): void
    {
        Router::$notFoundHandler = $handler;
    }

    protected static function runNotFoundHandler(): void
    {
        if (Router::$notFoundHandler != null) {
            call_user_func(Router::$notFoundHandler);
        } else {
            require_once FRAMEWORKPATH . '/Libraries/ErrorHandler/notfound.php';
        }
    }
}