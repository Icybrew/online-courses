<?php

namespace App\Http\Controllers;


/**
 * Class ErrorController
 * @package App\Http\Controllers
 */
class ErrorController extends Controller
{
    public function index()
    {
        return view("errors/error404");
    }
}
