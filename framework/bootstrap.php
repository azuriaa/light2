<?php

// Change current directory to root
chdir(ROOTPATH);

// Constants
define('APPPATH', ROOTPATH . '/app');
define('STOREPATH', ROOTPATH . '/store');
define('FRAMEWORKPATH', ROOTPATH . '/framework');

// Setup
ini_set('session.save_path', STOREPATH . '/session');
$_ENV = array_merge($_ENV, json_decode(file_get_contents(ROOTPATH . '/env.json'), true));

spl_autoload_register(function ($class) {
    $namespace = strtok($class, '\\');
    if ($namespace == 'App') {
        require_once ROOTPATH . '/' . $class . '.php';
    } elseif ($namespace == 'Light2') {
        require_once FRAMEWORKPATH . '/' . ltrim($class, $namespace) . '.php';
    }
});

// Helpers
function log_message(string $type, string $message): void
{
    error_log(ucfirst(strtolower($type)) . ": $message");
}

function base_url(string $uri = ''): string
{
    return 'http://' . $_ENV['host'] . '/' . $uri;
}

function current_url(): string
{
    $scheme = $_ENV['forceGlobalSecure'] == true ? 'https' : 'http';
    return $scheme . '://' . $_SERVER['HTTP_HOST'] . filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL);
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
            $dsn = 'mysql:dbname=' . $_ENV['pdo']['database'];
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
    $view->setup($file, $data, $extension);
    $view->render();
}

// App Config
date_default_timezone_set(\App\Config\App::$defaultTimezone);

// Run the app
Light2\Light2::runApp();