<?php

namespace App\Controllers;

use Light2\Controller;

class Home extends Controller
{
    public function index()
    {
        view('hello_world');
    }
}