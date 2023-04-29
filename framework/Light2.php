<?php

namespace Light2;

use App\Config\Routes;
use Light2\Libraries\Whoops\Run;
use Light2\Libraries\Whoops\Handler\PrettyPageHandler;

class Light2
{
    public static function runApp(): void
    {
        Light2::mountForceSecureHandler($_ENV['forceGlobalSecure']);
        Light2::mountErrorHandler($_ENV['environtment']);
        Routes::register();
        Router::useRouter();
    }

    protected static function mountForceSecureHandler(bool $force = false): void
    {
        if ($force == true) {
            if ($_SERVER['REQUEST_SCHEME'] != 'https') {
                header('Location: ' . current_url());
                exit(0);
            }
        }
    }

    protected static function mountErrorHandler(string $environtment): void
    {
        if ($environtment == 'production') {
            require_once FRAMEWORKPATH . '/Libraries/ErrorHandler/handler.php';
        } elseif ($environtment == 'development') {
            $whoops = new Run;
            $whoops->pushHandler(new PrettyPageHandler);
            $whoops->register();
            require_once FRAMEWORKPATH . '/Libraries/Kint/Kint.phar';
        }
    }
}