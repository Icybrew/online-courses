<?php

namespace App\Http\Controllers;

use App\Core\Config\Config;
use App\Core\Views\View;
use App\Order;
use Symfony\Component\HttpFoundation\Request;

use App\Course;
use App\CourseVideo;


/**
 * Class CoursesController
 * @package App\Http\Controllers
 */
class CoursesController extends Controller
{
    protected $courses;

    public function __construct(Request $request)
    {
        parent::__construct();
        $this->courses = Course::where('public', '=', 1)->getAll();

        $user = $request->getSession()->get('user');

        if (!is_null($user)) {
            $userOrders = Order::where('user_id', '=', $user->id)->getAll();

            array_walk($this->courses, function ($course) use ($userOrders) {
                $purchased = current(array_filter($userOrders, function ($userCourse) use ($course) {
                    return $userCourse->course_id == $course->id;
                }));

                $course->purchased = $purchased;
            });
        }

        View::share('courses', $this->courses);
    }

    public function index()
    {
        return view('courses/index');
    }

    public function show(Request $request, $course_id)
    {
        $course = current(array_filter($this->courses, function ($course) use ($course_id) {
            return $course->id == $course_id;
        }));

        if (!empty($course)) {

            if (isset($course->purchased) && $course->purchased) {
                $course->videos = CourseVideo::where('course_id', '=', $course->id)->orderBy("'order'")->getAll();
                return view('courses/course', ['course' => $course]);
            } else {
                return redirect()->route('courses.purchase', ['course' => $course->id]);
            }
        } else {
            return view('errors/error404');
        }
    }

    public function purchase($course_id)
    {
        $course = current(array_filter($this->courses, function ($course) use ($course_id) {
            return $course->id == $course_id;
        }));

        if (!empty($course)) {

            if (isset($course->purchased) && $course->purchased) {
                return redirect()->route('courses.show', ['course' => $course->id]);
            } else {
                return view('courses/purchase', ['course' => $course]);
            }
        } else {
            return view('errors/error404');
        }
    }

    public function purchasePaymentMethod(Request $request, $course_id)
    {
        $course = current(array_filter($this->courses, function ($course) use ($course_id) {
            return $course->id == $course_id;
        }));

        if (!empty($course)) {

            $user = $request->getSession()->get('user');

            $order = Order::where('user_id', '=', $user->id)
                ->where('course_id', '=', $course->id)
                ->get();

            if (!empty($order)) {
                return redirect()->route('courses.show', ['course' => $course->id]);
            }

            require_once __DIR__ . '/../../Core/paysera/includes.php';

            $price = $course->price_discount ? $course->price_discount : $course->price;

            $methods = \WebToPay::getPaymentMethodList(Config::get('paysera', 'project_id'), 'EUR')
                ->filterForAmount($price, 'EUR')
                ->setDefaultLanguage('lt');

            return view('courses/payment-method', ['course' => $course, 'methods' => $methods]);
        } else {
            return redirect()->route('courses.index');
        }
    }

    public function purchaseConfirm(Request $request, $id)
    {
        $paymentMethod = $request->request->get('payment-method');

        $errors = [];

        if (!isset($paymentMethod) || strlen($paymentMethod) == 0) {
            $errors['payment_method'] = 'Nepasirinktas atsiskaitymo būdas';
        }

        if (count($errors) != 0) {
            return redirect()->back()->withErrors($errors);
        }

        $course = Course::find($id);

        if (!empty($course)) {

            $user = $request->getSession()->get('user');

            $order = Order::where('user_id', '=', $user->id)
                ->where('course_id', '=', $course->id)
                ->get();

            if (!empty($order)) {
                return redirect()->route('courses.show', ['course' => $course->id]);
            }

            $price = isset($course->price_discount) ? $course->price_discount : $course->price;

            $order_id = Order::insert([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'price' => $price
            ]);

            if ($order_id) {
                require_once __DIR__ . '/../../Core/paysera/includes.php';

                \WebToPay::redirectToPayment([
                    'projectid' => Config::get('paysera', 'project_id'),
                    'sign_password' => Config::get('paysera', 'project_password'),
                    'test' => Config::get('paysera', 'test'),
                    'accepturl' => route('courses.purchase.accept', ['course' => $course->id]),
                    'cancelurl' => route('courses.purchase.cancel', ['course' => $course->id]),
                    'callbackurl' => route('courses.purchase.callback', ['course' => $course->id]),
                    'payment' => $paymentMethod,
                    'amount' => $price * 100,
                    'currency' => 'EUR',
                    'orderid' => $order_id,
                ]);
            } else {
                return redirect()->route('home')->withErrors(['error' => 'Something went wrong...']);
            }
        } else {
            return redirect()->route('courses.index');
        }
    }

    public function purchaseAccept(Request $request)
    {
        $errors = [];

        require_once __DIR__ . '/../../Core/paysera/includes.php';

        try {
            // Validating response
            $response = \WebToPay::validateAndParseData($request->query->all(), Config::get('paysera', 'project_id'), Config::get('paysera', 'project_password'));

            // Checking response status
            if ($response['status'] == 1) {

                // Getting order
                $order = Order::find($response['orderid']);

                // Validating that order exists
                if (empty($order)) {
                    return redirect()->route('home')->withErrors(['error' => sprintf("Order with id %s doesn't exist", $response['orderid'])]);
                }

                // Checking if order already completed
                if ($order->completed != 1) {

                    if ($response['test'] != Config::get('paysera', 'test')) {
                        return redirect()->route('home')->withErrors(['error' => 'Some values are not as expected']);
                    }

                    // Completing order
                    Order::update($order->id, [
                        'completed' => 1
                    ]);

                    return redirect()->route('courses.show', ['course' => $order->course_id]);
                } else {
                    return redirect()->route('courses.show', ['course' => $order->course_id]);
                }

            } else {
                return redirect()->route('home')->withErrors(['error' => 'Response status - ' . $response['status']]);
            }
        } catch (\Exception $exception) {
            return redirect()->route('home')->withErrors(['error' => $exception->getMessage()]);
        }
    }

    public function purchaseCancel(Request $request)
    {
        return view('courses/purchaseCancel');
    }

    public function purchaseCallback(Request $request, $course_id)
    {
        dc($request->query);
        dc($request->request);
        dd("Totally unexpected callback");
        $errors = [];

        require_once __DIR__ . '/../../Core/paysera/includes.php';

        try {
            // Validating response
            $response = \WebToPay::validateAndParseData($request->query->all(), Config::get('paysera', 'project_id'), Config::get('paysera', 'project_password'));

            // Getting order id
            $order_id = $response['orderid'] ?? null;

            // Validating order id
            if ($order_id || empty(Order::find($order_id))) {

                // Checking response status
                if ($response['status'] == 1) {
                    $order = Order::find($order_id);

                    if ($order->completed == 1) {
                        dd('Already bought');
                    } else {

                        if ($response['test'] != Config::get('paysera', 'test')) {
                            return redirect()->route('home')->withErrors(['error' => 'Some values are not as expected']);
                        }

                        // Completing order
                        Order::update($order->id, [
                            'completed' => 1
                        ]);

                        return  'OK';
                    }

                } else {
                    return redirect()->route('home')->withErrors(['error' => 'Response status - ' . $response['status']]);
                }
            } else {
                return redirect()->route('home')->withErrors(['error' => "Order with id '$order_id' doesn't exist"]);
            }
        } catch (\Exception $exception) {
            return redirect()->route('home')->withErrors(['error' => $exception->getMessage()]);
        }
    }
}
