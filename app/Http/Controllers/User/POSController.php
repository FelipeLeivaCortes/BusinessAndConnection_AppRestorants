<?php

namespace App\Http\Controllers\User;

use Ably\AblyRest;
use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use App\Models\CategoryModel as Category;
use App\Models\Customer;
use App\Models\Hall;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderTax;
use App\Models\ProductModel as Product;
use App\Models\Table;
use App\Utilities\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class POSController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware(function ($request, $next) {

            $route_name = request()->route()->getName();
            if ($route_name == 'pos.place_order' && $request->isMethod('post')) {
                $cart = new Cart($request->route('tableId'));
                if ($cart->getOrderId() == null) {
                    if (has_limit('orders', 'order_limit', false) <= 0) {
                        if (!$request->ajax()) {
                            return back()->with('error', _lang('Sorry, Your have already reached your package quota !'));
                        } else {
                            return response()->json(['result' => 'error', 'message' => _lang('Sorry, Your have already reached your package quota !')]);
                        }
                    }
                }
            }

            return $next($request);
        });
    }

    public function table(Request $request) {
        $halls = Hall::with('tables')->active()->get();
        $cart = Cart::getCart();

        if (count($halls) == 0) {
            return redirect()->route('halls.index')->with('error', _lang('You need to create at least one hall !'));
        }

        if ($request->ajax()) {
            return view('backend.user.pos.ajax.table', compact('halls', 'cart'));
        }
        return view('backend.user.pos.table', compact('halls', 'cart'));
    }

    public function pos(Request $request, $tableId, $type = 'table') {
        if ($type == 'delivery' && get_business_option('order_delivery_status', 0, business_id()) != 1) {
            if ($request->ajax()) {
                return response()->json(_lang('Order delivery is not enabled !'), 401);
            }
            return back()->with('error', _lang('Order delivery is not enabled !'));
        }

        if ($type == 'takeway' && get_business_option('order_takeway_status', 0, business_id()) != 1) {
            if ($request->ajax()) {
                return response()->json(_lang('Order takeway is not enabled !'), 401);
            }
            return back()->with('error', _lang('Order takeway is not enabled !'));
        }

        $data               = array();
        $data['tableId']    = $tableId;
        $data['type']       = $type;
        $data['categories'] = Cache::remember('category_list' . business_id(), 60 * 480, function () {
            return Category::active()->orderBy('position')->get();
        });
        $data['products'] = Cache::remember('products_list' . business_id(), 60 * 480, function () {
            return Product::active()->with('category', 'addon_products', 'product_options', 'variation_prices')->orderBy('name')->get();
        });

        if ($type == 'table') {
            $data['table'] = Table::with('hall')->find($tableId);
        }

        $cart = new Cart($tableId, $type);
        if ($cart->getItems()->count() == 0 && $cart->getTaxes()->count() == 0) {
            $cart->applyDefaultTax(); // Apply Default Tax
        }
        $data['cartItems']     = $cart->getItems();
        $data['subTotal']      = $cart->getSubTotal();
        $data['discount']      = $cart->getDiscount();
        $data['taxes']         = $cart->getTaxes();
        $data['serviceCharge'] = $cart->getServiceCharge();
        $data['grandTotal']    = $cart->getGrandTotal();
        $data['orderStatus']   = $cart->getOrderStatus();
        $data['needUpdate']    = $cart->getNeedUpdate();
        $data['orderId']       = $cart->getOrderId();

        if ($request->ajax()) {
            return view('backend.user.pos.ajax.pos', $data);
        }
        return view('backend.user.pos.pos', $data);
    }

    public function active_orders(Request $request) {
        $data            = array();
        $data['allCart'] = Cart::getCart();

        if ($request->ajax()) {
            return view('backend.user.pos.ajax.active-orders', $data);
        }
        return view('backend.user.pos.active-orders', $data);
    }

    public function product(Request $request, $productId) {
        if (!$request->ajax()) {
            return back();
        }

        if (Cache::has('products_list' . business_id())) {
            $products = Cache::get('products_list' . business_id());
            $product  = $products->find($productId);
        } else {
            $product = Product::with('addon_products', 'product_options', 'variation_prices')->active()->find($productId);
        }

        return view('backend.user.pos.ajax.product', compact('product'));
    }

    public function add_to_cart(Request $request, $product_id, $table_id) {
        if (!$request->ajax()) {
            return back();
        }

        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
        }

        if (Cache::has('products_list' . business_id())) {
            $products = Cache::get('products_list' . business_id());
            $product  = $products->find($product_id);
        } else {
            $product = Product::find($product_id);
        }

        //Get Variation Product Price
        $variation  = get_variation_price($request->product_option, $product_id);
        $price      = $variation['price'];
        $attributes = $variation['attributes'];

        //Get Addone Product Price
        $addOnProducts = get_addon_price($request->product_addon_id, $product_id);
        $add_on_prices = $addOnProducts['price'];
        $add_on_items  = $addOnProducts['add_on_items'];

        //If price is zero then get general product
        if ($price == 0) {
            $price = $product->special_price > 0 ? $product->special_price : $product->price;
        }
        $unit_price = $price + $add_on_prices; // Unit Price

        $cartId      = md5(json_encode($attributes) . json_encode($add_on_items) . $product->id);
        $productName = $product->name;

        if (!empty($attributes)) {
            $productName .= ' (';
            foreach ($attributes as $key => $attribute) {
                $productName .= $key . ': ' . $attribute . ', ';
            }
            $productName = substr($productName, 0, -2); // Remove last Char
            $productName .= ')';
        }

        if (!empty($add_on_items)) {
            $productName .= ' (';
            foreach ($add_on_items as $add_on_item) {
                $productName .= $add_on_item . ' + ';
            }
            $productName = substr($productName, 0, -3); // Remove last Char
            $productName .= ')';
        }

        $cart = new Cart($table_id);
        $cart->addItem($cartId, [
            "product_id"   => $product->id,
            "name"         => $productName,
            "quantity"     => $request->quantity,
            "unit_price"   => formatAmount($unit_price, currency_symbol($request->activeBusiness->currency)),
            "raw_price"    => $unit_price,
            "total_price"  => $unit_price * $request->quantity,
            "image"        => $product->image,
            "attributes"   => $attributes,
            "add_on_items" => $add_on_items,
            "product"      => $product->toArray(),
            "table_id"     => $table_id,
        ]);

        return response()->json([
            'result'        => true,
            'cartItems'     => $cart->getItems(),
            'subTotal'      => $cart->getSubTotal(true),
            'grandTotal'    => $cart->getGrandTotal(true),
            'discount'      => $cart->getDiscount(),
            'taxes'         => $cart->getTaxes(),
            'serviceCharge' => $cart->getServiceCharge(),
            'needUpdate'    => $cart->getNeedUpdate(),
        ]);

    }

    public function update_cart(Request $request, $cartId, $table_id) {
        $cart = new Cart($table_id);
        $cart->updateQuantity($cartId, $request->quantity);
        return response()->json([
            'result'        => true,
            'cartItems'     => $cart->getItems(),
            'subTotal'      => $cart->getSubTotal(true),
            'grandTotal'    => $cart->getGrandTotal(true),
            'discount'      => $cart->getDiscount(),
            'taxes'         => $cart->getTaxes(),
            'serviceCharge' => $cart->getServiceCharge(),
            'needUpdate'    => $cart->getNeedUpdate(),
        ]);
    }

    public function remove_cart(Request $request, $cartId, $table_id) {
        $cart = new Cart($table_id);
        $cart->removeCart($cartId);

        return response()->json([
            'result'        => true,
            'cartItems'     => $cart->getItems(),
            'subTotal'      => $cart->getSubTotal(true),
            'grandTotal'    => $cart->getGrandTotal(true),
            'discount'      => $cart->getDiscount(),
            'taxes'         => $cart->getTaxes(),
            'serviceCharge' => $cart->getServiceCharge(),
            'needUpdate'    => $cart->getNeedUpdate(),
        ]);
    }

    public function empty_cart(Request $request, $table_id) {
        $cart = new Cart($table_id);
        if ($cart->getOrderId() != null) {
            $order = Order::find($cart->getOrderId());
            if ($order) {
                $order->delete();
            }

            if (get_option('live_order_api', 'ajax') == 'ably') {
                $client  = new AblyRest(get_option('ably_api_key'));
                $channel = $client->channel('order');
                $channel->publish('orderUpdated', json_encode($order));
            }
        }
        $cart->emptyCart();
        return back();
    }

    public function add_discount(Request $request, $table_id) {
        if (!$request->ajax()) {
            return back();
        }

        if ($request->isMethod('get')) {
            $cart     = new Cart($table_id);
            $discount = $cart->getDiscount('percentage');
            return view('backend.user.pos.ajax.discount', compact('table_id', 'discount'));
        } else {
            $cart = new Cart($table_id);
            if ($cart->getItems()->count() == 0) {
                return response()->json(['result' => false, 'message' => _lang('Cart is empty !')]);
            }
            $cart->addDiscount($request->discount);

            return response()->json([
                'result'        => true,
                'cartItems'     => $cart->getItems(),
                'subTotal'      => $cart->getSubTotal(true),
                'grandTotal'    => $cart->getGrandTotal(true),
                'discount'      => $cart->getDiscount(),
                'taxes'         => $cart->getTaxes(),
                'serviceCharge' => $cart->getServiceCharge(),
                'needUpdate'    => $cart->getNeedUpdate(),
            ]);
        }
    }

    public function apply_tax(Request $request, $table_id) {
        $cart = new Cart($table_id);
        $cart->applyTax($request->taxes);

        return response()->json([
            'result'        => true,
            'cartItems'     => $cart->getItems(),
            'subTotal'      => $cart->getSubTotal(true),
            'grandTotal'    => $cart->getGrandTotal(true),
            'discount'      => $cart->getDiscount(),
            'taxes'         => $cart->getTaxes(),
            'serviceCharge' => $cart->getServiceCharge(),
            'needUpdate'    => $cart->getNeedUpdate(),
        ]);
    }

    public function place_order(Request $request, $table_id) {
        if (!$request->ajax()) {return back();}

        if ($request->isMethod('get')) {
            $data                = array();
            $cart                = new Cart($table_id);
            $data['table_id']    = $table_id;
            $data['orderStatus'] = $cart->getOrderStatus();

            if ($cart->getOrderStatus() == 5) {
                $cart->updateOrderStatus(5, $cart->getOrderId());
                return response()->json(['result' => false, 'message' => _lang('Order is already completed !')]);
            }

            $data['orderType'] = $cart->getOrderType();
            if ($data['orderType'] == 'table') {
                $data['defaultStatus'] = get_business_option('pos_default_status', 0, business_id());
            } else if ($data['orderType'] == 'delivery') {
                $data['defaultStatus'] = get_business_option('delivery_default_status', 0, business_id());
            } else {
                $data['defaultStatus'] = get_business_option('takeway_default_status', 0, business_id());
            }
            $data['grandTotal'] = formatAmount($cart->getGrandTotal());
            $data['paidAmount'] = 0;

            if ($cart->getOrderId() != null) {
                $data['order'] = Order::where('id', $cart->getOrderId())->first();
                if ($data['order']) {
                    $data['paidAmount'] = formatAmount($data['order']->paid);
                }
            }

            return view('backend.user.pos.ajax.place-order', $data);
        } else {
            $cart = new Cart($table_id);

            $validator = Validator::make($request->all(), [
                'payment_method' => 'required',
                'status'         => 'required',
                'amount'         => 'nullable|required_if:status,5|numeric',
                'customer_id'    => $cart->getOrderType() != 'table' ? 'required' : 'nullable',
            ], [
                'amount.required_if'      => _lang('Payment required before completing this order !'),
                'customer_id.required_if' => _lang('Customer field is required'),
            ]);

            if ($validator->fails()) {
                return response()->json(['result' => false, 'message' => $validator->errors()->all()]);
            }

            if ($cart->getGrandTotal() == 0) {
                return response()->json(['result' => false, 'message' => _lang('No items found in the cart !')]);
            }

            if ($cart->getOrderId() != null) {
                $existingOrder = Order::where('id', $cart->getOrderId())->first();
            }

            if ($cart->getGrandTotal() > $request->amount && $request->status == 5) {
                return response()->json(['result' => false, 'message' => _lang('Payment required before completing this order !')]);
            }

            $table = Table::with('hall')->find($table_id);

            DB::beginTransaction();
            if ($cart->getOrderId() == null) {
                $order               = new Order();
                $order->order_number = get_business_option('invoice_number', '100001');
            } else {
                $order = $existingOrder;
                $order->items()->whereNotIn('cart_id', $cart->getItems()->keys()->toArray())->delete();
                $order->taxes()->whereNotIn('id', $cart->getTaxes()->pluck('id'))->delete();
            }
            $order->sub_total           = $cart->getSubTotal();
            $order->discount_percentage = $cart->getDiscount('percentage');
            $order->discount            = $cart->getDiscount('rawAmount');
            $order->service_charge      = $cart->getServiceCharge('rawAmount');
            $order->grand_total         = $cart->getGrandTotal();
            if ($request->amount > 0) {
                $order->paid = $request->amount;
            }
            $order->note           = $request->note;
            $order->status         = $request->status;
            $order->payment_method = $request->payment_method;
            $order->order_type     = $cart->getOrderType();
            $order->delivery_time  = $request->delivery_time;
            $order->table_id       = $table_id;

            if ($order->order_type == 'table') {
                $order->table = _lang('Hall') . ': ' . $table->hall->name . ', ' . _lang('Table No') . ': ' . $table->table_no;
            }

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

            //Store Order Items
            foreach ($cart->getItems() as $cartId => $cartItem) {
                $order->items()->save(OrderItem::firstOrNew(
                    [
                        'order_id'     => $order->id,
                        'product_id'   => $cartItem['product_id'],
                        'cart_id'      => $cartId,
                        'product_name' => $cartItem['name'],
                    ], [
                        'description' => $cartItem['product']['description'],
                        'quantity'    => $cartItem['quantity'],
                        'unit_cost'   => $cartItem['raw_price'],
                        'sub_total'   => ($cartItem['raw_price'] * $cartItem['quantity']),
                    ]));
            }

            //Store Taxes
            foreach ($cart->getTaxes() as $tax) {
                $order->taxes()->save(OrderTax::firstOrNew(
                    [
                        'order_id' => $order->id,
                        'tax_id'   => $tax['id'],
                    ], [
                        'name'   => $tax['name'],
                        'rate'   => $tax['rate'],
                        'amount' => $tax['rawAmount'],
                    ]));
            }

            //Increment Invoice Number
            $message = _lang('Order Updated');
            if ($cart->getOrderId() == null) {
                BusinessSetting::where('name', 'invoice_number')->increment('value');
                $cart->addOrderNumber($order->order_number);
                $message = _lang('New Order Placed');
            }
            $cart->needUpdate(false);
            $cart->updateOrderStatus($request->status, $order->id);

            $link        = route('pos.sell', [$table_id, $order->order_type]);
            $printLink   = null;
            $orderStatus = $order->status;
            if ($order->status == 5) {
                $message   = _lang('Order Completed');
                $printLink = route('pos.print_customer_receipt', $order->id);
            }

            DB::commit();

            if ($order->id > 0) {
                if (get_option('live_order_api', 'ajax') == 'ably') {
                    $client  = new AblyRest(get_option('ably_api_key'));
                    $channel = $client->channel('order');
                    $channel->publish('orderUpdated', json_encode($order));
                }
                return response()->json(['result' => true, 'message' => $message, 'orderStatus' => $orderStatus, 'link' => $link, 'printLink' => $printLink]);
            } else {
                return response()->json(['result' => false, 'message' => _lang('Something wrong, Please try again !')]);
            }

        }
    }

    public function print_customer_receipt(Request $request, $order_id) {
        $order = Order::with('items', 'taxes')->find($order_id);
        $title = 'customer-receipt-#' . $order->order_number;
        return view('backend.user.pos.print-customer-receipt', compact('order', 'title'));

        //$pdf   = Pdf::loadView('backend.user.pos.print-customer-receipt', compact('order', 'title'));
        //$pdf->setPaper('a7', 'portrait');
        //return $pdf->stream('customer-receipt-#' . $order->order_number . '.pdf');
    }

    public function print_kitchen_receipt(Request $request, $order_id) {
        $order = Order::with('items', 'taxes')->find($order_id);
        $title = 'customer-receipt-#' . $order->order_number;
        return view('backend.user.pos.print-kitchen-receipt', compact('order', 'title'));
    }

}