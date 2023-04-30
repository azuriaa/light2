<?php

namespace Light2;

use App\Config\Routes;
use Light2\Libraries\Whoops\Run;
use Light2\Libraries\Whoops\Handler\PrettyPageHandler;

class Light2
{
    public static function runApp(): void
    {
        self::mountForceSecureHandler($_ENV['forceGlobalSecure']);
        self::mountErrorHandler($_ENV['environtment']);
        Routes::register();
        Router::run();
    }

    protected static function mountForceSecureHandler(bool $force = false): void
    {
        if ($force == true && $_SERVER['REQUEST_SCHEME'] != 'https') {
            header('Location: ' . current_url());
            exit(0);
        }
    }

    protected static function mountErrorHandler(string $environtment): void
    {
        if ($environtment == 'production') {
            require_once FRAMEWORKPATH . '\\Libraries\\ErrorHandler\\error_handler.php';
        } elseif ($environtment == 'development') {
            $whoops = new Run;
            $whoops->pushHandler(new PrettyPageHandler);
            $whoops->register();
            require_once FRAMEWORKPATH . '\\Libraries\\Kint\\Kint.phar';
        }
    }
}