<?php

namespace Light2;

use Light2\Services\RequestService;
use Light2\Services\ResponseService;

abstract class Controller
{
    protected RequestService $request;
    protected ResponseService $response;

    public function __construct()
    {
        $this->request = service(RequestService::class);
        $this->response = service(ResponseService::class);
    }
}