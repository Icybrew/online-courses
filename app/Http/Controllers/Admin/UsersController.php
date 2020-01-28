<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Order;
use App\User;
use DrewM\MailChimp\MailChimp;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class UsersController
 * @package App\Http\Controllers\Admin
 */
class UsersController extends Controller
{
    public function index()
    {
        $users = User::select('*')
            ->all();

        $purchases = Order::select('*')
            ->join('courses', 'courses.id', '=', 'orders.course_id')
            ->all();

        array_walk($users, function ($user) use (&$purchases) {
            $user->purchases = [];

            array_walk($purchases, function ($purchase) use (&$user) {
                if ($purchase->user_id == $user->id) {
                    $user->purchases[] = $purchase;
                }
            });
        });

        return view('admin/users/index', ['users' => $users]);
    }

    public function show($id)
    {
        $user = User::find($id);

        if (!empty($user)) {
            return view('admin/users/user', ['user' => $user]);
        } else {
            return view('errors/error404');
        }
    }

    public function sendMail(Request $request, MailChimp $mailChimp, $id)
    {
        $user = User::find($id);

        if (!empty($user)) {

            $lists = $mailChimp->get('lists');

            if (!$mailChimp->success()) {
                return redirect()->back()->withErrors(['error' => $mailChimp->getLastError()]);
            }

            $list_id = $lists['lists'][0]['id'] ?? null;

            $result = $mailChimp->post("lists/$list_id/members", [
                'email_address' => $user->email,
                'status' => 'subscribed',
            ]);

            if ($mailChimp->success()) {
                return redirect()->back()->with(['success' => sprintf('%s prenumeracija užsakyta', $user->email)]);
            } else {
                return redirect()->back()->withErrors(['error' => $mailChimp->getLastError()]);
            }
        } else {
            return view('errors/error404');
        }
    }
}
