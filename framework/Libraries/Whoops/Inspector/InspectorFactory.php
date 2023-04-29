<?php

namespace Light2\Libraries\Whoops\Inspector;

use Light2\Libraries\Whoops\Exception\Inspector;

class InspectorFactory implements InspectorFactoryInterface
{
    /**
     * @param \Throwable $exception
     * @return InspectorInterface
     */
    public function create($exception)
    {
        return new Inspector($exception, $this);
    }
}