<?php

namespace Light2\Factories;

class InstanceFactory
{
    protected static array $instances = [];

    public static function mountInstance($service)
    {
        if (!array_key_exists($service, self::$instances)) {
            array_push(self::$instances, $service);
            self::$instances[$service] = new $service;
        }

        return self::$instances[$service];
    }

    public static function getNamedInstance(string $name)
    {
        if (array_key_exists($name, self::$instances)) {
            return self::$instances[$name];
        }

        return null;
    }

    public static function registerNamedInstance(string $name, $instance): bool
    {
        if (!array_key_exists($name, self::$instances)) {
            array_push(self::$instances, $name);
            self::$instances[$name] = $instance;
            return true;
        }

        return false;
    }
}