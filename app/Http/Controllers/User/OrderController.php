<?php

namespace App\Http\Controllers\User;

use Ably\AblyRest;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Utilities\Cart;
use Carbon\Carbon;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $assets = ['datatable'];
        return view('backend.user.order.list', compact('assets'));
    }

    public function get_table_data() {
        $orders = Order::select('orders.*');

        return Datatables::eloquent($orders)
            ->editColumn('sub_total', function ($order) {
                return formatAmount($order->sub_total, currency_symbol(request()->activeBusiness->currency));
            })
            ->editColumn('grand_total', function ($order) {
                return formatAmount($order->grand_total, currency_symbol(request()->activeBusiness->currency));
            })
            ->editColumn('paid', function ($order) {
                return formatAmount($order->paid, currency_symbol(request()->activeBusiness->currency));
            })
            ->editColumn('status', function ($order) {
                return order_status($order->status);
            })
            ->editColumn('order_type', function ($order) {
                return ucwords($order->order_type);
            })
            ->addColumn('action', function ($order) {
                return '<div class="dropdown text-center">'
                . '<button class="btn btn-outline-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">' . _lang('Action')
                . '</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item" target="_blank" href="' . route('pos.print_customer_receipt', $order['id']) . '"><i class="fas fa-print mr-1"></i> ' . _lang('POS Receipt') . '</a>'
                . '<a class="dropdown-item" href="' . route('orders.show', $order['id']) . '"><i class="fas fa-info-circle mr-1"></i> ' . _lang('Order Details') . '</a>'
                . '<a class="dropdown-item ajax-modal" href="' . route('orders.edit', $order['id']) . '" class="ajax-modal" data-title="' . _lang('Update Order') . '"><i class="fas fa-pencil-alt mr-1"></i> ' . _lang('Update Order') . '</a>'
                . '<form action="' . route('orders.destroy', $order['id']) . '" method="post">'
                . csrf_field()
                . '<input name="_method" type="hidden" value="DELETE">'
                . '<button class="dropdown-item btn-remove" type="submit"><i class="fas fa-minus-circle mr-1"></i> ' . _lang('Delete') . '</button>'
                    . '</form>'
                    . '</div>'
                    . '</div>';
            })
            ->setRowId(function ($order) {
                return "row_" . $order->id;
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function tracking($action = '') {
        if ($action == 'fetch') {
            $orderStatus  = ['Pending', 'Accepted', 'Preparing', 'Ready', 'Delivered', 'Completed'];
            $statusColors = ['#ff4757', '#10ac84', '#5f27cd', '#341f97', '#3742fa', '#be2edd'];

            $ordersList = [];
            $orders     = Order::where('status', '!=', 5)
            //->whereDay('created_at', date('d'))
                ->where('created_at', '>=', Carbon::now()->subDay())
                ->orderBy('status')
                ->get();
            foreach ($orders as $order) {
                $order->color                               = $statusColors[$order->status];
                $order->total                               = formatAmount($order->grand_total, currency_symbol(request()->activeBusiness->currency));
                $order->edit_link                           = route('orders.edit', $order->id);
                $order->print_link                          = route('pos.print_customer_receipt', $order->id);
                $order->view_link                           = route('orders.show', $order->id);
                $ordersList[$orderStatus[$order->status]][] = $order;
            }
            return response()->json($ordersList);
        } else {
            $orderStatus  = ['Pending', 'Accepted', 'Preparing', 'Ready', 'Delivered', 'Completed'];
            $statusColors = ['#ff4757', '#10ac84', '#5f27cd', '#341f97', '#3742fa', '#be2edd'];

            $ordersList = [];
            $orders     = Order::where('status', '!=', 5)
            //->whereDay('created_at', date('d'))
                ->where('created_at', '>=', Carbon::now()->subDay())
                ->orderBy('status')
                ->get();
            foreach ($orders as $order) {
                $order->color                               = $statusColors[$order->status];
                $order->total                               = formatAmount($order->grand_total, currency_symbol(request()->activeBusiness->currency));
                $ordersList[$orderStatus[$order->status]][] = $order;
            }

            $currency_symbol = currency_symbol(request()->activeBusiness->currency);

            return view('backend.user.order.tracking', compact('ordersList', 'currency_symbol'));
        }

    }

    public function update_order_status(Request $request, $id) {
        if ($request->isOwner == false && !has_permission('orders.tracking')) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => _lang('Permission denied !')]);
            } else {
                return back()->with('error', _lang('Permission denied !'));
            }
        }
        $validator = Validator::make($request->all(), [
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
        }
        $order         = Order::find($id);
        $order->status = $request->status;
        $order->save();

        //Update Cart when update status from Order Tracking
        $cartOrder = Cart::findByOrderId($order->id);
        if ($cartOrder != null) {
            $table_id = array_keys($cartOrder)[0];
            $cart     = new Cart($table_id);
            $cart->updateOrderStatus($order->status, $order->id);
        }

        if (get_option('live_order_api', 'ajax') == 'ably') {
            $client  = new AblyRest(get_option('ably_api_key'));
            $channel = $client->channel('order');
            $channel->publish('orderUpdated', json_encode($order));
        }

        if (!$request->ajax()) {
            return redirect()->route('orders.index')->with('success', _lang('Order Updated'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Order Updated')]);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id) {
        $order = Order::find($id);
        return view('backend.user.order.view', compact('order', 'id'));
    }

    public function edit(Request $request, $id) {
        if (!$request->ajax()) {return back();}
        $order = Order::find($id);
        return view('backend.user.order.modal.edit', compact('order', 'id'));
    }

    public function update(Request $request, $id) {
        if (!$request->ajax()) {return back();}

        $order = Order::find($id);

        $validator = Validator::make($request->all(), [
            'payment_method' => 'required',
            'status'         => 'required',
            'amount'         => 'nullable|required_if:status,5|numeric',
            'customer_id'    => $order->order_type != 'table' ? 'required' : 'nullable',
        ], [
            'amount.required_if'      => _lang('Payment required before completing this order !'),
            'customer_id.required_if' => _lang('Customer field is required'),
        ]);

        if ($validator->fails()) {
            return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
        }

        if ($order->grand_total > $request->amount && $request->status == 5) {
            return response()->json(['result' => 'error', 'message' => _lang('Payment required before completing this order !')]);
        }

        $order->paid           = $request->amount;
        $order->note           = $request->note;
        $order->status         = $request->status;
        $order->payment_method = $request->payment_method;
        $order->delivery_time  = $request->delivery_time;

        if ($request->customer_id != null) {
            $customer                = Customer::find($request->customer_id);
            $order->customer_id      = $customer->id;
            $order->customer_name    = $customer->name;
            $order->customer_email   = $customer->email;
            $order->customer_phone   = $customer->phone;
            $order->customer_city    = $customer->city;
            $order->customer_state   = $customer->state;
            $order->customer_address = $customer->address;
        }

        $order->save();

        //Update Cart when update status from Order Tracking
        $cartOrder = Cart::findByOrderId($order->id);
        if ($cartOrder != null) {
            $table_id = array_keys($cartOrder)[0];
            $cart     = new Cart($table_id);
            $cart->updateOrderStatus($order->status, $order->id, false); //Unset = false
        }

        if (get_option('live_order_api', 'ajax') == 'ably') {
            $client  = new AblyRest(get_option('ably_api_key'));
            $channel = $client->channel('order');
            $channel->publish('orderUpdated', json_encode($order));
        }

        if (!$request->ajax()) {
            return redirect()->route('orders.index')->with('success', _lang('Updated Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Updated Successfully'), 'data' => $order, 'table' => '#orders_table']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $order = Order::find($id);
        $order->delete();
        return redirect()->route('orders.index')->with('success', _lang('Deleted Successfully'));
    }
}
