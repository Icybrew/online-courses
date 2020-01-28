<?php

namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Request;

use App\User;


/**
 * Class LoginController
 * @package App\Http\Controllers\Auth
 */
class LoginController extends Controller
{
    public function show(Request $request)
    {
        return view("auth/login");
    }

    public function login(Request $request)
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');

        $errors = [];

        // Validation
        if (!isset($email)) {
            $errors['email'] = 'Neivestas elektroninis paštas';
        }

        if (!isset($password)) {
            $errors['password'] = 'Neivestas slaptažodis';
        }

        if (count($errors) > 0) {
            return redirect()->back()->withInput()->withErrors($errors);
        }

        $user = User::select('users.*, roles.name as role')
            ->where('email', '=', $email)
            ->where('password', '=', md5($password))
            ->leftJoin('roles', 'roles.id', '=', 'users.role_id')
            ->get();

        if (!empty($user)) {
            $request->getSession()->set('user', $user);
            return redirect()->route('home');
        } else {
            $errors['email'] = 'Neteisingas elektroninis paštas arba slaptažodis';
            return redirect()->back()->withInput()->withErrors($errors);
        }
    }

    public function logout(Request $request)
    {
        $request->getSession()->set('user', null);
        return redirect()->route('home');
    }
}
