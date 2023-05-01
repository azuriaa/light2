<?php

// Change current directory to root
chdir(ROOTPATH);

// Constants
define('APPPATH', ROOTPATH . '\\app');
define('STOREPATH', ROOTPATH . '\\store');
define('FRAMEWORKPATH', ROOTPATH . '\\framework');

// Setup
ini_set('log_errors', true);
ini_set('error_log', STOREPATH . '\\logs\\' . date('Y-m-d') . '.log');
ini_set('session.save_path', STOREPATH . '/session');
$_ENV = array_merge($_ENV, json_decode(file_get_contents(ROOTPATH . '\\env.json'), true));

spl_autoload_register(function ($class) {
    $namespace = strtok($class, '\\');
    if ($namespace == 'App') {
        require_once ROOTPATH . '\\' . $class . '.php';
    } elseif ($namespace == 'Light2') {
        require_once FRAMEWORKPATH . '\\' . ltrim($class, $namespace) . '.php';
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
    return Light2\Factories\InstanceFactory::mountInstance($service);
}

function model(string $model): \Light2\Model
{
    return service("\\App\\Models\\$model");
}

function db_connect($dsn = null, $username = null, $password = null): \Light2\Libraries\FluentPDO\Query
{
    if (is_null($dsn) && is_null($username) && is_null($password)) {
        $dsn = $_ENV['pdo']['dsn'];
        $username = $_ENV['pdo']['username'];
        $password = $_ENV['pdo']['password'];
    }

    if (is_null(\Light2\Factories\InstanceFactory::getNamedInstance($dsn))) {
        \Light2\Factories\InstanceFactory::registerNamedInstance(
            $dsn,
            new Light2\Libraries\FluentPDO\Query(
                new \PDO(
                    \Light2\Factories\DSNFactory::create(
                        $dsn,
                        ROOTPATH . '\\store\\'
                    ),
                    $username,
                    $password
                )
            )
        );
    }

    return \Light2\Factories\InstanceFactory::getNamedInstance($dsn);
}

function view(string $file, array $data = []): void
{
    $view = service(Light2\Services\RendererService::class);
    $view->setup(APPPATH . '\\Views\\', $file, $data);
    $view->render();
}

// App Config
date_default_timezone_set(\App\Config\App::$defaultTimezone);

if (!\App\Config\App::$exposePHP) {
    header_remove('X-Powered-By');
}

if (\App\Config\App::$autoloadComposer) {
    require_once ROOTPATH . '\\vendor\\autoload.php';
}

// Run the app
Light2\Light2::runApp();