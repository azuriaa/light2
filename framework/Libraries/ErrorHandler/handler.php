<?php

function error_handler($err = null, $str = null, $file = null, $line = null)
{
    if (is_null($str) && is_null($file) && is_null($line)) {
        error_log($err);
    } else {
        error_log("$err - $str at $file line $line");
    }
    include 'error.php';
}

set_exception_handler('error_handler');
set_error_handler('error_handler');