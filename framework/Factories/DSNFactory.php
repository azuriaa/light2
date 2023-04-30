<?php

namespace Light2\Factories;

class DSNFactory
{
    public static function create(string $dsn, string $path): string
    {
        if (strtok($dsn, ':') == 'sqlite' && $dsn != 'sqlite::memory:') {
            $dsn = 'sqlite:' . $path . str_replace('sqlite:', '', $dsn);
        }

        return $dsn;
    }
}