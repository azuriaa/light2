<?php

namespace Light2\Services;

class InstanceService
{
    protected static array $instances = [];

    public static function mountInstance($service)
    {
        if (!array_key_exists($service, InstanceService::$instances)) {
            array_push(InstanceService::$instances, $service);
            InstanceService::$instances[$service] = new $service;
        }

        return InstanceService::$instances[$service];
    }

    public static function getNamedInstance(string $name)
    {
        if (array_key_exists($name, InstanceService::$instances)) {
            return InstanceService::$instances[$name];
        }

        return null;
    }

    public static function registerNamedInstance(string $name, $instance): bool
    {
        if (!array_key_exists($name, InstanceService::$instances)) {
            array_push(InstanceService::$instances, $name);
            InstanceService::$instances[$name] = $instance;
            return true;
        }

        return false;
    }
}