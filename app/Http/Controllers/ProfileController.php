<?php

namespace App\Http\Controllers;


use App\Core\Views\View;
use App\Course;
use App\Order;
use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use Symfony\Component\HttpFoundation\Request;

use App\User;


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
            $userOrders = Order::where('user_id', '=', $user->id)->all();

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
        $purchases = Order::select('orders.*, courses.name')->where('user_id', '=', $user->id)->join('courses', 'courses.id', '=', 'orders.course_id')->groupBy('orders.id')->all();

        return view('profile/purchases', ['purchases' => $purchases]);
    }

    public function purchaseShow(Request $request, Mpdf $mpdf, $id)
    {
        $user = $request->getSession()->get('user');

        $order = Order::find($id);

        if (!empty($order) && $order->user_id == $user->id) {

            $order->course = Course::find($order->course_id);

            $style = '

';

            $body = '
<h1 style="text-align: center;">Sąskaitos faktūra</h1>
<table width="100%" style="margin-top: 25px; font-family: serif;" cellpadding="10">
    <tr>
        <td width="45%" style="border: 0.1mm solid #888888; "><span style="font-size: 14pt; font-weight: bold; font-family: sans;">Pardavėjas:</span><br /><br />Internetiniai kursai<br />Ozo g. 25<br />+370 612 34567</td>
        <td width="10%">&nbsp;</td>
        <td width="45%" style="border: 0.1mm solid #888888;"><span style="font-size: 14pt; font-weight: bold; font-family: sans;">Pirkėjas:</span><br /><br />' . $user->email . '<br />...</td>
    </tr>
</table>
<table width="100%" style="margin-top: 50px; font-family: serif; border-collapse: collapse;" cellpadding="10">
    <tr>
        <th style="border: 1px solid black;">Eil. Nr.</th>
        <th style="border: 1px solid black;">Prekės, paslaugos pavadinimas</th>
        <th style="border: 1px solid black;">Kaina</th>
    </tr>
    <tr>
        <td style="border: 1px solid black;">'. $order->id .'</td>
        <td style="border: 1px solid black;">'. $order->course->name .'</td>
        <td style="border: 1px solid black; text-align: right;">'. $order->price .'</td>
    </tr>
    <tr>
        <td style="border: 1px solid black; font-weight: bold; text-align: right;" colspan="2">Viso:</td>
        <td style="border: 1px solid black; font-weight: bold; text-align: right;">' . $order->price . '</td>
    </tr>
</table>
';

            $mpdf->SetTitle('Sąskaitos faktūra');
            $mpdf->WriteHTML($style, HTMLParserMode::HEADER_CSS);
            $mpdf->WriteHTML($body, HTMLParserMode::HTML_BODY);
            $mpdf->Output('saskaitos-faktura', Destination::INLINE);
        } else {
            return view('errors/error404');
        }
    }
}
