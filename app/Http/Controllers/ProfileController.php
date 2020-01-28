<?php

namespace App\Http\Controllers;


use App\Core\Views\View;
use App\Course;
use App\Order;
use Symfony\Component\HttpFoundation\Request;

use App\User;
use App\UserCourse;


/**
 * Class ProfileController
 * @package App\Http\Controllers
 */
class ProfileController extends Controller
{

    public function __construct(Request $request)
    {
        parent::__construct();
        $courses = Course::all();

        $user = $request->getSession()->get('user');

        if (!is_null($user)) {
            $userOrders = Order::where('user_id', '=', $user->id)->getAll();

            array_walk($courses, function ($course) use ($userOrders) {
                $purchased = current(array_filter($userOrders, function ($userCourse) use ($course) {
                    return $userCourse->course_id == $course->id;
                }));

                $course->purchased = $purchased;
            });
        }

        View::share('courses', $courses);
    }

    public function index()
    {
        return view('profile/index');
    }

    public function editPassword()
    {
        return view('profile/edit/password');
    }

    public function updatePassword(Request $request)
    {
        $user = $request->getSession()->get('user');

        $password = $request->request->get('password');
        $password_new = $request->request->get('password_new');
        $password_new_confirmation = $request->request->get('password_new_confirmation');

        $errors = [];

        if (!isset($password)) {
            $errors['password'] = 'Neivestas slaptažodis';
        } else if (md5($password) != $user->password) {
            $errors['password'] = 'Neteisingas slaptažodis';
        }

        if (!isset($password_new)) {
            $errors['password_new'] = 'Neivestas naujas slaptažodis';
        } else if (strlen($password_new) < 4) {
            $errors['password_new'] = 'Naujas slaptažodis negali būti trumpesnis negu 4 simboliai';
        }

        if ($password_new != $password_new_confirmation) {
            $errors['password_new_confirmation'] = 'Slaptažodžiai nesutampa';
        }

        if (count($errors) == 0) {
            User::update($user->id, [
                'password' => md5($password_new)
            ]);

            return redirect()->route('profile.index')->with(['success' => 'Slaptažodis pakeistas']);
        } else {
            return redirect()->back()->withErrors($errors);
        }
    }

    public function purchases(Request $request)
    {
        $user = $request->getSession()->get('user');
        $purchases = Order::select('orders.*, courses.name')->where('user_id', '=', $user->id)->join('courses', 'courses.id', '=', 'orders.course_id')->groupBy('orders.id')->getAll();

        return view('profile/purchases', ['purchases' => $purchases]);
    }
}
