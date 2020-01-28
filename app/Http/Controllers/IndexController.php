<?php

namespace App\Http\Controllers;


use Symfony\Component\HttpFoundation\Request;

use App\Course;
use App\Order;


/**
 * Class IndexController
 * @package App\Http\Controllers
 */
class IndexController extends Controller
{
    public function index(Request $request)
    {
        $courses = Course::where('public', '=', 1)->getAll();

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

        return view('home', ['courses' => $courses]);
    }
}
