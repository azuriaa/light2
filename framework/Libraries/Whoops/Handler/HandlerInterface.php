<?php

namespace Light2\Libraries\Whoops\Handler;

use Light2\Libraries\Whoops\Inspector\InspectorInterface;
use Light2\Libraries\Whoops\RunInterface;

interface HandlerInterface
{
    /**
     * @return int|null A handler may return nothing, or a Handler::HANDLE_* constant
     */
    public function handle();

    /**
     * @param  RunInterface  $run
     * @return void
     */
    public function setRun(RunInterface $run);

    /**
     * @param  \Throwable $exception
     * @return void
     */
    public function setException($exception);

    /**
     * @param  InspectorInterface $inspector
     * @return void
     */
    public function setInspector(InspectorInterface $inspector);
}