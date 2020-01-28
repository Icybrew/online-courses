<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Order;


/**
 * Class StatisticsController
 * @package App\Http\Controllers\Admin
 */
class StatisticsController extends Controller
{
    public function index()
    {
        return view('admin/statistics/index');
    }

    public function income()
    {
        $purchases = Order::select('*')
            ->join('courses', 'courses.id', '=', 'orders.course_id')
            ->join('users', 'users.id', '=', 'orders.user_id')
            ->all();

        $income = new \stdClass();

        $income->month = 0;
        $income->quarter = 0;
        $income->halfyear = 0;
        $income->year = 0;

        // TODO refactor income statistics
        //dd(UserCourse::select('*')->between('MONTH(purchased_at)', 1, 1)->all());
        //dd(UserCourse::select('*, YEAR(current_date) as year')->where('YEAR(purchased_at)', '=', 'YEAR(current_date)')->all());

        $lastmonth = date('Y-m-d H:i:s', strtotime("-1 months"));
        $lastQuarter = date('Y-m-d H:i:s', strtotime("-3 months"));
        $lastHalf = date('Y-m-d H:i:s', strtotime("-6 months"));
        $lastYear = date('Y-m-d H:i:s', strtotime("-1 years"));

        array_walk($purchases, function ($purchase) use ($income, $lastmonth, $lastQuarter, $lastHalf, $lastYear) {
            if ($purchase->created_at > $lastmonth) {
                $income->month += $purchase->price;
            }
            if ($purchase->created_at > $lastQuarter) {
                $income->quarter += $purchase->price;
            }
            if ($purchase->created_at > $lastHalf) {
                $income->halfyear += $purchase->price;
            }
            if ($purchase->created_at > $lastYear) {
                $income->year += $purchase->price;
            }
        });

        return view('admin/statistics/income', ['income' => $income]);
    }
}
