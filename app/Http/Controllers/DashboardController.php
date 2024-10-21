<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payroll;
use App\Models\SubscriptionPayment;
use App\Models\Transaction;
use App\Models\User;

class DashboardController extends Controller {
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        date_default_timezone_set(get_option('timezone', 'Asia/Dhaka'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index() {
        $user           = auth()->user();
        $user_type      = $user->user_type;
        $data           = array();
        $data['assets'] = ['datatable'];

        if ($user_type == 'admin') {
            $data['total_user']  = User::where('user_type', 'user')->count();
            $data['total_owner'] = User::where('user_type', 'user')
                ->where('package_id', '!=', null)
                ->where('membership_type', '!=', null)
                ->count();

            $data['trial_users'] = User::where('user_type', 'user')
                ->where('package_id', '!=', null)
                ->where('membership_type', 'trial')
                ->count();

            $data['expired_users'] = User::where('user_type', 'user')
                ->where('package_id', '!=', null)
                ->where('membership_type', '!=', null)
                ->whereDate('valid_to', '<', now())
                ->count();

            $data['recentPayments'] = SubscriptionPayment::select('subscription_payments.*')
                ->with('user', 'package', 'created_by')
                ->orderBy("subscription_payments.id", "desc")
                ->limit(10)
                ->get();

            $data['newUsers'] = User::where('user_type', 'user')
                ->with('package')
                ->where('package_id', '!=', null)
                ->where('membership_type', '!=', null)
                ->orderBy("users.id", "desc")
                ->limit(10)
                ->get();

            return view("backend.admin.dashboard-admin", $data);
        } else if ($user_type == 'user') {
            $date  = date('Y-m-d');
            $month = date('m');
            $year  = date('Y');

            $data['current_month_sales'] = Order::selectraw('IFNULL(SUM(orders.grand_total), 0) as amount')
                ->whereRaw("MONTH(orders.created_at)='$month' AND YEAR(orders.created_at)='$year'")
                ->where('paid', '>', 0)
                ->where('status', 5)
                ->first()->amount;

            $data['current_day_sales'] = Order::selectraw('IFNULL(SUM(orders.grand_total), 0) as amount')
                ->whereRaw("date(orders.created_at)='$date'")
                ->where('paid', '>', 0)
                ->where('status', 5)
                ->first()->amount;

            $data['current_month_expenses'] = Transaction::selectraw('IFNULL(SUM(transactions.amount), 0) as amount')
                ->whereRaw("MONTH(transactions.trans_date)='$month' AND YEAR(transactions.trans_date)='$year'")
                ->where('dr_cr', 'dr')
                ->first()->amount;

            $data['current_month_orders'] = Order::whereRaw("MONTH(orders.created_at)='$month' AND YEAR(orders.created_at)='$year'")
                ->where('paid', '>', 0)
                ->where('status', 5)
                ->count();

            if (request('isOwner') == true) {
                return view("backend.user.dashboard-user", $data);
            }
            return view("backend.user.dashboard-staff", $data);
        }

    }

    public function current_month_sales_widget() {
        // Use for Permission Only
        return redirect()->route('dashboard.index');
    }

    public function current_day_sales_widget() {
        // Use for Permission Only
        return redirect()->route('dashboard.index');
    }

    public function current_month_expense_widget() {
        // Use for Permission Only
        return redirect()->route('dashboard.index');
    }

    public function current_month_orders_widget() {
        // Use for Permission Only
        return redirect()->route('dashboard.index');
    }

    public function cashflow_widget() {
        // Use for Permission Only
        return redirect()->route('dashboard.index');
    }

    public function sales_by_category_widget() {
        // Use for Permission Only
        return redirect()->route('dashboard.index');
    }

    public function expense_by_category_widget() {
        // Use for Permission Only
        return redirect()->route('dashboard.index');
    }

    public function json_sales_by_category() {
        $orders = Order::selectRaw('order_type, ROUND(IFNULL(SUM(grand_total),0),2) as amount')
            ->whereRaw('YEAR(created_at) = ?', date('Y'))
            ->groupBy('order_type')
            ->get();

        $category = array();
        $colors   = array();
        $amounts  = array();

        $colorPickers = ['table' => '#5f27cd', 'delivery' => '#ff9f43', 'takeway' => '#ff6b6b'];

        foreach ($orders as $order) {
            array_push($category, ucwords($order->order_type));
            array_push($colors, $colorPickers[$order->order_type]);
            array_push($amounts, (double) $order->amount);
        }

        echo json_encode(array('amounts' => $amounts, 'category' => $category, 'colors' => $colors));
    }

    public function json_expense_by_category() {
        $transactions = Transaction::selectRaw('transaction_category_id, ref_id, ref_type, ROUND(IFNULL(SUM(transactions.amount),2)) as amount')
            ->with('transaction_category')
            ->where('dr_cr', 'dr')
            ->whereRaw('YEAR(trans_date) = ?', date('Y'))
            ->groupBy('transaction_category_id', 'ref_type')
            ->get();

        $category = array();
        $colors   = array();
        $amounts  = array();

        foreach ($transactions as $transaction) {
            array_push($category, $transaction->transaction_category->name);
            array_push($colors, $transaction->transaction_category->color);
            array_push($amounts, (double) $transaction->amount);
        }

        echo json_encode(array('amounts' => $amounts, 'category' => $category, 'colors' => $colors));
    }

    public function json_cashflow() {
        $months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

        $orders = Order::selectRaw('MONTH(created_at) as order_month, ROUND(IFNULL(SUM(grand_total),0),2) as grand_total')
            ->whereRaw('YEAR(created_at) = ?', date('Y'))
            ->groupBy('order_month')
            ->get();

        $payroll = Payroll::whereRaw("year = ?", [date('Y')])
            ->selectRaw("month, ROUND(IFNULL(SUM(net_salary),0),2) as staff_salary")
            ->where('status', 1)
            ->groupBy('month')
            ->get();

        $transactions = Transaction::selectRaw('MONTH(trans_date) as td, dr_cr, type, ROUND(IFNULL(SUM(transactions.amount),0),2) as amount')
            ->whereRaw('YEAR(trans_date) = ?', date('Y'))
            ->groupBy('td', 'type')
            ->get();

        $sales        = array();
        $expenses     = array();
        $staff_salary = array();

        foreach ($orders as $order) {
            $sales[$order->order_month] = $order->grand_total;
        }

        foreach ($transactions as $transaction) {
            if ($transaction->type == 'expense') {
                $expenses[$transaction->td] = $transaction->amount;
            }
        }

        foreach ($payroll as $salary) {
            $staff_salary[$salary->month] = $salary->staff_salary;
        }

        echo json_encode(array('month' => $months, 'sales' => $sales, 'expenses' => $expenses, 'staff_salary' => $staff_salary));
    }

    public function json_package_wise_subscription() {
        if (auth()->user()->user_type != 'admin') {
            return null;
        }
        $users = User::selectRaw('package_id, COUNT(id) as subscribed')
            ->with('package')
            ->where('user_type', 'user')
            ->where('package_id', '!=', null)
            ->groupBy('package_id')
            ->get();

        $package    = array();
        $colors     = array();
        $subscribed = array();

        $flatColors = ["#1abc9c", "#2ecc71", "#3498db", "#9b59b6", "#34495e",
            "#16a085", "#27ae60", "#2980b9", "#8e44ad", "#2c3e50",
            "#f1c40f", "#e67e22", "#e74c3c", "#ecf0f1", "#95a5a6",
            "#f39c12", "#d35400", "#c0392b", "#bdc3c7", "#7f8c8d"];

        foreach ($users as $user) {
            array_push($package, $user->package->name . ' (' . ucwords($user->package->package_type) . ')');
            //array_push($colors, sprintf('#%06X', mt_rand(0, 0xFFFFFF)));
            $index = array_rand($flatColors, 1);
            array_push($colors, $flatColors[$index]);
            unset($flatColors[$index]);
            array_push($subscribed, (double) $user->subscribed);
        }

        echo json_encode(array('package' => $package, 'subscribed' => $subscribed, 'colors' => $colors));
    }

    public function json_yearly_reveneu() {
        $months               = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        $subscriptionPayments = SubscriptionPayment::selectRaw('MONTH(created_at) as td, SUM(subscription_payments.amount) as amount')
            ->whereRaw('YEAR(created_at) = ?', date('Y'))
            ->groupBy('td')
            ->get();

        $transactions = array();

        foreach ($subscriptionPayments as $subscriptionPayment) {
            $transactions[$subscriptionPayment->td] = $subscriptionPayment->amount;
        }

        echo json_encode(array('month' => $months, 'transactions' => $transactions));
    }
}
