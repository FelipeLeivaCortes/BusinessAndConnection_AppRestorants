<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\Order;
use App\Models\Payroll;
use App\Models\ProductModel as Product;
use App\Models\Purchase;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller {

    public function sales_report(Request $request) {
        if ($request->isMethod('get')) {
            $page_title = _lang('Sales Report');
            return view('backend.user.reports.sales_report', compact('page_title'));
        } else if ($request->isMethod('post')) {
            @ini_set('max_execution_time', 0);
            @set_time_limit(0);

            $data  = array();
            $date1 = $request->date1;
            $date2 = $request->date2;

            $data['report_data'] = Order::select('orders.*')
                ->when($request->created_user_id != '', function ($query) {
                    return $query->where('created_user_id', request('created_user_id'));
                })
                ->when($request->status != '', function ($query) {
                    return $query->where('status', request('status'));
                })
                ->whereRaw("date(orders.created_at) >= '$date1' AND date(orders.created_at) <= '$date2'")
                ->orderBy('orders.created_at', 'desc')
                ->get();

            $data['date1']           = $request->date1;
            $data['date2']           = $request->date2;
            $data['created_user_id'] = $request->created_user_id;
            $data['status']          = $request->status;
            $data['page_title']      = _lang('Sales Report');
            $data['currency_symbol'] = currency_symbol($request->activeBusiness->currency);
            return view('backend.user.reports.sales_report', $data);
        }
    }

    public function item_wise_sales_report(Request $request) {
        if ($request->isMethod('get')) {
            $page_title = _lang('Item Wise Sales Report');
            return view('backend.user.reports.item_wise_sales_report', compact('page_title'));
        } else if ($request->isMethod('post')) {
            @ini_set('max_execution_time', 0);
            @set_time_limit(0);

            $data  = array();
            $date1 = $request->date1;
            $date2 = $request->date2;

            $data['report_data'] = Product::select('products.id', 'products.name')
                ->join('order_items', 'products.id', 'order_items.product_id')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->when($request->status != '', function ($query) {
                    return $query->where('orders.status', request('status'));
                })
                ->selectRaw('SUM(order_items.quantity) as quantity')
                ->selectRaw('SUM(order_items.sub_total) as total')
                ->whereRaw("date(orders.created_at) >= '$date1' AND date(orders.created_at) <= '$date2'")
                ->groupBy('products.id')
                ->get();

            $data['date1']           = $request->date1;
            $data['date2']           = $request->date2;
            $data['status']          = $request->status;
            $data['page_title']      = _lang('Sales Report');
            $data['currency_symbol'] = currency_symbol($request->activeBusiness->currency);
            return view('backend.user.reports.item_wise_sales_report', $data);
        }
    }

    public function profit_and_loss(Request $request) {
        if ($request->isMethod('get')) {
            $page_title = _lang('Profit & Loss Report');
            return view('backend.user.reports.profit_and_loss', compact('page_title'));
        } else if ($request->isMethod('post')) {
            @ini_set('max_execution_time', 0);
            @set_time_limit(0);

            $data  = array();
            $date1 = $request->date1;
            $date2 = $request->date2;

            $data['sales'] = Order::with('items')
                ->selectRaw("SUM(sub_total) as sub_total, SUM(service_charge) as service_charge, SUM(discount) as discount")
                ->whereRaw("date(orders.created_at) >= '$date1' AND date(orders.created_at) <= '$date2'")
                ->where('orders.status', '=', 5)
                ->first();

            $purchases = Purchase::with('items')
                ->whereRaw("date(purchase_date) >= '$date1' AND date(purchase_date) <= '$date2'")
                ->where('purchases.paid', '>', 0)
                ->get();

            $data['purchase_amount']   = 0;
            $data['purchase_discount'] = 0;
            foreach ($purchases as $purchase) {
                $percentage = (100 / $purchase->grand_total) * $purchase->paid;
                foreach ($purchase->items as $purchase_item) {
                    $data['purchase_amount'] += ($percentage / 100) * $purchase_item->sub_total;
                }
                $data['purchase_discount'] += ($percentage / 100) * $purchase->discount;
            }

            $data['othersExpense'] = Transaction::with('transaction_category')
                ->selectRaw('transaction_category_id, ROUND(IFNULL(SUM(transactions.amount),0),2) as amount')
                ->where('dr_cr', 'dr')
                ->where('ref_id', null)
                ->whereRaw("date(trans_date) >= '$date1' AND date(trans_date) <= '$date2'")
                ->groupBy('transaction_category_id')
                ->get();

            $data['payroll'] = Payroll::whereRaw("month >= ? AND month <= ? AND year >= ? AND year <= ?", [date('m', strtotime($date1)), date('m', strtotime($date2)), date('Y', strtotime($date1)), date('Y', strtotime($date2))])
                ->selectRaw("IFNULL(SUM(net_salary),0) as staff_salary, IFNULL(SUM(expense),0) as expense_claim")
                ->where('status', 1)
                ->first();

            $data['date1']       = $request->date1;
            $data['date2']       = $request->date2;
            $data['report_data'] = true;
            $data['currency']    = request()->activeBusiness->currency;
            $data['page_title']  = _lang('Profit & Loss Report');
            return view('backend.user.reports.profit_and_loss', $data);
        }

    }

    public function attendance_report(Request $request) {
        if ($request->isMethod('get')) {
            $page_title = _lang('Attendance Report');
            return view('backend.user.reports.attendance_report', compact('page_title'));
        } else if ($request->isMethod('post')) {
            @ini_set('max_execution_time', 0);
            @set_time_limit(0);
            $data  = array();
            $month = $request->month;
            $year  = $request->year;

            $data['calendar'] = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $attendance_list  = Attendance::select('attendance.*')
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->orderBy('date', 'asc')
                ->orderBy('employee_id', 'asc')
                ->get();

            $holidays = Holiday::whereMonth('date', $month)
                ->whereYear('date', $year)
                ->orderBy('date', 'ASC')
                ->pluck('date')
                ->toArray();

            $data['employees'] = Employee::active()
                ->orderBy('employees.id', 'asc')
                ->get();

            $weekends    = json_decode(get_business_option('weekends', '[]', business_id()));
            $report_data = [];

            for ($day = 1; $day <= $data['calendar']; $day++) {
                $date   = date('Y-m-d', strtotime("$year-$month-$day"));
                $status = ['A', 'P', 'L', 'W', 'H'];

                foreach ($attendance_list as $attendance) {
                    if (in_array($date, $holidays)) {
                        $report_data[$attendance->employee_id][$day] = $status[4]; // Holiday
                    } else {
                        if ($date == $attendance->getRawOriginal('date')) {
                            $report_data[$attendance->employee_id][$day] = $status[$attendance->status];
                        } else {
                            if (in_array(date('l', strtotime($date)), $weekends)) {
                                $report_data[$attendance->employee_id][$day] = $status[3];
                            }
                        }
                    }
                }

            }

            $data['month']           = $request->month;
            $data['year']            = $request->year;
            $data['page_title']      = _lang('Attendance Report');
            $data['report_data']     = $report_data;
            $data['attendance_list'] = $attendance_list;
            return view('backend.user.reports.attendance_report', $data);
        }
    }

    public function expense_report(Request $request) {
        if ($request->isMethod('get')) {
            $page_title = _lang('Expense Report');
            return view('backend.user.reports.expense_report', compact('page_title'));
        } else if ($request->isMethod('post')) {
            @ini_set('max_execution_time', 0);
            @set_time_limit(0);

            $data  = array();
            $date1 = $request->date1;
            $date2 = $request->date2;

            $data['report_data'] = Transaction::select('transactions.*')
                ->with(['transaction_category'])
                ->when(request('transaction_category_id') != '', function ($query) {
                    return $query->where('transaction_category_id', request('transaction_category_id'));
                })
                ->whereRaw("date(transactions.trans_date) >= '$date1' AND date(transactions.trans_date) <= '$date2'")
                ->where('dr_cr', 'dr')
                ->orderBy('transactions.trans_date', 'desc')
                ->get();

            $data['date1']                   = $request->date1;
            $data['date2']                   = $request->date2;
            $data['transaction_category_id'] = $request->transaction_category_id;
            $data['page_title']              = _lang('Expense Report');
            $data['currency_symbol']         = currency_symbol(request()->activeBusiness->currency);
            return view('backend.user.reports.expense_report', $data);
        }
    }

    public function purchase_report(Request $request) {
        if ($request->isMethod('get')) {
            $page_title = _lang('Purchase By Vendors');
            return view('backend.user.reports.purchase_report', compact('page_title'));
        } else if ($request->isMethod('post')) {
            @ini_set('max_execution_time', 0);
            @set_time_limit(0);

            $data      = array();
            $date1     = $request->date1;
            $date2     = $request->date2;
            $vendor_id = isset($request->vendor_id) ? $request->vendor_id : '';

            $data['report_data'] = Purchase::with('vendor')
                ->selectRaw('vendor_id, SUM(grand_total) as total_income, sum(paid) as total_paid')
                ->when($vendor_id, function ($query, $vendor_id) {
                    return $query->where('vendor_id', $vendor_id);
                })
                ->whereRaw("date(purchase_date) >= '$date1' AND date(purchase_date) <= '$date2'")
                ->groupBy('vendor_id')
                ->get();

            $data['date1']      = $request->date1;
            $data['date2']      = $request->date2;
            $data['vendor_id']  = $request->vendor_id;
            $data['currency']   = request()->activeBusiness->currency;
            $data['page_title'] = _lang('Purchase By Vendors');
            return view('backend.user.reports.purchase_report', $data);
        }
    }

    public function payroll_report(Request $request) {
        if (package()->payroll_module != 1) {
            if (!$request->ajax()) {
                return back()->with('error', _lang('Sorry, This module is not available in your current package !'));
            } else {
                return response()->json(['result' => 'error', 'message' => _lang('Sorry, This module is not available in your current package !')]);
            }
        }
        
        if ($request->isMethod('get')) {
            $page_title = _lang('Payroll Report');
            return view('backend.user.reports.payroll_report', compact('page_title'));
        } else if ($request->isMethod('post')) {
            @ini_set('max_execution_time', 0);
            @set_time_limit(0);

            $data  = array();
            $month = $request->month;
            $year  = $request->year;

            $data['report_data'] = Payroll::with('staff')
                ->select('payslips.*')
                ->where('month', $month)
                ->where('year', $year)
                ->get();

            $data['month']      = $request->month;
            $data['year']       = $request->year;
            $data['currency']   = request()->activeBusiness->currency;
            $data['page_title'] = _lang('Payroll Report');
            return view('backend.user.reports.payroll_report', $data);
        }
    }

    public function tax_report(Request $request) {
        if ($request->isMethod('get')) {
            $page_title = _lang('Tax Report');
            return view('backend.user.reports.tax_report', compact('page_title'));
        } else if ($request->isMethod('post')) {
            @ini_set('max_execution_time', 0);
            @set_time_limit(0);

            $data        = array();
            $date1       = $request->date1;
            $date2       = $request->date2;
            $report_type = $request->report_type;
            $business_id = request()->activeBusiness->id;

            if ($report_type == 'paid_unpaid') {

                $data['sales_taxes'] = DB::select("SELECT taxes.id, taxes.name, taxes.rate, taxes.tax_number, SUM(orders.sub_total) as sales_amount,
                SUM(order_taxes.amount) as sales_tax FROM orders LEFT JOIN order_taxes ON orders.id=order_taxes.order_id
                AND orders.business_id = $business_id AND orders.status != 0
                AND DATE(orders.created_at) >= '$date1' AND DATE(orders.created_at) <= '$date2' RIGHT JOIN taxes ON taxes.id=order_taxes.tax_id WHERE taxes.business_id = $business_id GROUP BY taxes.id");

                $data['purchase_taxes'] = DB::select("SELECT taxes.id, taxes.name, taxes.rate, taxes.tax_number, SUM(purchases.sub_total) as purchase_amount,
                SUM(purchase_taxes.amount) as purchase_tax FROM purchases LEFT JOIN purchase_taxes ON purchases.id=purchase_taxes.purchase_id
                AND purchases.business_id = $business_id AND purchases.purchase_date >= '$date1' AND purchases.purchase_date <= '$date2'
                RIGHT JOIN taxes ON taxes.id=purchase_taxes.tax_id WHERE taxes.business_id = $business_id GROUP BY taxes.id");
            }

            if ($report_type == 'paid') {
                $data['sales_taxes'] = DB::select("SELECT taxes.id, taxes.name, taxes.rate, taxes.tax_number, SUM(orders.sub_total) as sales_amount,
                SUM(order_taxes.amount) as sales_tax FROM orders LEFT JOIN order_taxes ON orders.id=order_taxes.order_id
                AND orders.business_id = $business_id AND orders.status = 5
                AND DATE(orders.created_at) >= '$date1' AND DATE(orders.created_at) <= '$date2' RIGHT JOIN taxes ON taxes.id=order_taxes.tax_id WHERE taxes.business_id = $business_id GROUP BY taxes.id");

                $data['purchase_taxes'] = DB::select("SELECT taxes.id, taxes.name, taxes.rate, taxes.tax_number, SUM((((100 / purchases.grand_total) * purchases.paid) / 100) * purchases.sub_total) as purchase_amount,
                SUM((((100 / purchases.grand_total) * purchases.paid) / 100) * purchase_taxes.amount) as purchase_tax FROM purchases LEFT JOIN purchase_taxes ON purchases.id=purchase_taxes.purchase_id
                AND purchases.business_id = $business_id AND purchases.paid > 0 AND purchases.purchase_date >= '$date1' AND purchases.purchase_date <= '$date2'
                RIGHT JOIN taxes ON taxes.id=purchase_taxes.tax_id WHERE taxes.business_id = $business_id GROUP BY taxes.id");
            }

            $data['date1']       = $request->date1;
            $data['date2']       = $request->date2;
            $data['report_type'] = $request->report_type;
            $data['currency']    = request()->activeBusiness->currency;
            $data['page_title']  = _lang('Tax Report');
            $data['report_data'] = true;
            return view('backend.user.reports.tax_report', $data);
        }
    }

}