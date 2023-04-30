<?php

function error_handler($err = null, $str = null, $file = null, $line = null)
{
    if (is_null($str) && is_null($file) && is_null($line)) {
        error_log($err);
    } else {
        switch ($err) {
            case E_ERROR:
                $err = 'E_ERROR';
                break;
            case E_WARNING:
                $err = 'E_WARNING';
                break;
            case E_PARSE:
                $err = 'E_PARSE';
                break;
            case E_NOTICE:
                $err = 'E_NOTICE';
                break;
        }
        error_log("$err: $str at $file line $line");
    }
    require_once 'error.php';
}

error_reporting(E_ALL);
ini_set('display_errors', false);
set_exception_handler('error_handler');
set_error_handler('error_handler');