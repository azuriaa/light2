<?php

namespace Light2;

use Light2\Services\Request;
use Light2\Services\Response;

abstract class Controller
{
    protected Request $request;
    protected Response $response;

    public function __construct()
    {
        $this->request = service(Request::class);
        $this->response = service(Response::class);
    }
}