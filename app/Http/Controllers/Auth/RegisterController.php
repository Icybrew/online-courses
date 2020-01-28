<?php

namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Request;

use App\User;


/**
 * Class RegisterController
 * @package App\Http\Controllers\Auth
 */
class RegisterController extends Controller
{
    public function show()
    {
        return view("auth/register");
    }

    public function register(Request $request)
    {
        $firstname = $request->request->get('firstname');
        $lastname = $request->request->get('lastname');
        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $password_confirm = $request->request->get('password_confirmation');

        $errors = [];

        // Validation
        if (!isset($firstname)) {
            $errors['firstname'] = 'Neivestas vardas';
        } else if (strlen($firstname) <= 3) {
            $errors['firstname'] = 'Vardas negali būti trumpesnis negu 3 simboliai';
        }

        if (!isset($lastname)) {
            $errors['lastname'] = 'Neivesta pavardė';
        } else if (strlen($lastname) < 4) {
            $errors['lastname'] = 'Pavardė negali būti trumpesnis negu 3 simboliai';
        }

        if (!isset($email)) {
            $errors['email'] = 'Neivestas elektroninis paštas';
        } else if (strlen($email) <= 3) {
            $errors['email'] = 'Elektroninis paštas negali būti trumpesnis negu 4 simboliai';
        } else if (!empty(User::where('email', '=', $email)->get())) {
            $errors['email'] = 'Vartotojas su tokiu elektroniniu paštu jau egzistuoja';
        }

        if (!isset($password)) {
            $errors['password'] = 'Neivestas slaptažodis';
        } else if (strlen($password) <= 3) {
            $errors['password'] = 'Slaptažodis negali būti trumpesnis negu 3 simboliai';
        } else if ($password !== $password_confirm) {
            $errors['password'] = 'Slaptažodžiai turi sutapti';
        }

        if (count($errors) > 0) {
            return redirect()->back()->withInput()->withErrors($errors);
        }

        $result = User::insert([
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
            'password' => md5($password)
        ]);

        return redirect()->route('login.show')->withInput();
    }
}
