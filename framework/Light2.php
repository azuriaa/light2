<?php

namespace Light2;

use App\Config\Routes;
use Light2\Libraries\Whoops\Run;
use Light2\Libraries\Whoops\Handler\PrettyPageHandler;

class Light2
{
    public static function runApp(): void
    {
        if ($_ENV['forceGlobalSecure'] == true) {
            Light2::forceSecure();
        }

        if ($_ENV['environtment'] == 'development') {
            Light2::runDevelopmentToolService();
        } else {
            Light2::runProductionErrorHandler();
        }

        Routes::register();
        Router::useRouter();
    }

    protected static function forceSecure(): void
    {
        if ($_SERVER['REQUEST_SCHEME'] != 'https') {
            header('Location: ' . current_url());
            exit(0);
        }
    }

    protected static function runDevelopmentToolService(): void
    {
        // Whoops
        $whoops = new Run;
        $whoops->pushHandler(new PrettyPageHandler);
        $whoops->register();

        // Kint
        require_once FRAMEWORKPATH . '/Libraries/Kint/Kint.phar';
    }

    protected static function runProductionErrorHandler(): void
    {
        require_once FRAMEWORKPATH . '/Libraries/ErrorHandler/handler.php';
    }
}