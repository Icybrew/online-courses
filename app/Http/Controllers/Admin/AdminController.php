<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;


/**
 * Class AdminController
 * @package App\Http\Controllers\Admin
 */
class AdminController extends Controller
{
    public function index()
    {
        return view('admin/index');
    }
}
