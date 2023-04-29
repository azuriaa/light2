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
}