<?php

// Change current directory to root
chdir(ROOTPATH);

// Constants
define('APPPATH', ROOTPATH . '/app');
define('STOREPATH', ROOTPATH . '/store');
define('FRAMEWORKPATH', ROOTPATH . '/framework');

// Setup
error_reporting(E_ALL);
ini_set('log_errors', true);
ini_set('error_log', STOREPATH . '/logs/' . date('Y-m-d') . '.log');
ini_set('session.save_path', STOREPATH . '/session');

if (file_exists(ROOTPATH . '/env.json')) {
    $_ENV = json_decode(file_get_contents(ROOTPATH . '/env.json'), true);
    define('BASEURL', $_ENV['baseURL']);
    ini_set('display_errors', $_ENV['environtment'] == 'development' ? true : false);
}

spl_autoload_register(function ($class) {
    $namespace = strtok($class, '\\');
    if ($namespace == 'App') {
        require_once ROOTPATH . '/' . $class . '.php';
    } elseif ($namespace == 'Light2') {
        require_once FRAMEWORKPATH . '/' . ltrim($class, $namespace) . '.php';
    }
});

// App Config
date_default_timezone_set(\App\Config\App::$defaultTimezone);

// Helpers
function base_url(string $uri = ''): string
{
    return 'http://' . BASEURL . '/' . $uri;
}

function current_url(): string
{
    $scheme = $_ENV['forceGlobalSecure'] == true ? 'https' : 'http';
    return $scheme . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

function service(string $service)
{
    return Light2\Services\InstanceService::mountInstance($service);
}

function model(string $model)
{
    return service("\App\Models\\$model");
}

function db_connect($dsn = null, $username = null, $password = null)
{
    if (is_null($dsn) && is_null($username) && is_null($password)) {
        $username = $_ENV['pdo']['username'];
        $password = $_ENV['pdo']['password'];

        if ($_ENV['pdo']['driver'] == 'mysql') {
            $dsn = 'mysql:host=' . $_ENV['pdo']['host'] . ';dbname=' . $_ENV['pdo']['name'];
        } elseif ($_ENV['pdo']['driver'] == 'sqlite') {
            $dsn = 'sqlite:' . ROOTPATH . '//store//' . $_ENV['pdo']['name'];
        } else {
            $dsn = $_ENV['pdo']['driver'] . ':' . $_ENV['pdo']['name'];
        }
    }

    return new Light2\Libraries\FluentPDO\Query(
        new \PDO($dsn, $username, $password)
    );
}

function view(string $file, array $data = [], string $extension = '.php'): void
{
    $view = service(Light2\Services\RendererService::class);
    $view->setParams($file, $data, $extension);
    $view->render();
}

// Run the app
Light2\Light2::runApp();