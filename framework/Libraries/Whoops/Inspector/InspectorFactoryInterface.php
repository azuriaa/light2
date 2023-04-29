<?php

namespace Light2\Libraries\Whoops\Inspector;

interface InspectorFactoryInterface
{
    /**
     * @param \Throwable $exception
     * @return InspectorInterface
     */
    public function create($exception);
}