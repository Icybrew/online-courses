<?php

namespace App\Http\Controllers;


use App\Core\Config\Config;
use App\Core\Views\View;

/**
 * Class Controller
 * @package App\Http\Controllers
 */
class Controller
{
    public function __construct()
    {
        View::share('title', Config::get('app', 'name'));
    }
}
