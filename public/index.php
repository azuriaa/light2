<?php

// Project directory
define('HOME_DIR', realpath(__DIR__ . "/../project"));

error_reporting(E_ALL);
ini_set('log_errors', true);
ini_set('error_log', HOME_DIR . '/store/errors.log');
ini_set('session.save_path', HOME_DIR . '/store');

// Enable or disable error display
ini_set('display_errors', true);

require_once HOME_DIR . '/lib/core.php';
require_once HOME_DIR . '/main.php';

// URI prefix
Router::$prefix = '/light/public';

Application::main();
