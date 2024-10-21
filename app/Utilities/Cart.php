<?php
namespace App\Utilities;

use App\Models\Order;
use App\Models\Tax;

class Cart {

    private $cart          = [];
    public $tableId        = null;
    private $serviceCharge = [
        'percentage' => 0,
        'amount'     => 0,
        'rawAmount'  => 0,
    ];
    private $discount = [
        'percentage' => 0,
        'amount'     => 0,
        'rawAmount'  => 0,
    ];
    private $taxes      = [];
    private $subTotal   = 0;
    private $grandTotal = 0;
    private $currency   = '';

    public function __construct($tableId, $orderType = 'table') {
        $this->cart    = session()->get('cart');
        $this->tableId = $tableId;

        if (!isset($this->cart[$tableId])) {
            $this->cart[$tableId] = [
                'items'         => [],
                'taxes'         => [],
                'serviceCharge' => [
                    'percentage' => 0,
                    'amount'     => 0,
                    'rawAmount'  => 0,
                ],
                'discount'      => [
                    'percentage' => 0,
                    'amount'     => 0,
                    'rawAmount'  => 0,
                ],
                'order_id'      => null,
                'order_number'  => null,
                'order_status'  => null,
                'order_type'    => $orderType,
                'need_update'   => false,
            ];
            session()->put('cart', $this->cart);
        }
        $this->calculateSummary();
    }

    public static function getCart() {
        static::getExistingOrders();

        if (session()->has('cart')) {
            return session()->get('cart');
        }
        return [];
    }

    public static function getExistingOrders() {
        $existingOrders = Order::where('status', '!=', 5)->get();

        foreach ($existingOrders as $order) {
            if($order->table_id == null){
                continue;
            }
            $cart                                         = new Cart($order->table_id);
            $cart->cart[$order->table_id]['order_id']     = $order->id;
            $cart->cart[$order->table_id]['order_type']   = $order->order_type;
            $cart->cart[$order->table_id]['order_number'] = $order->order_number;
            //$cart->cart[$order->tableId]['order_id'] = $order->id;

            foreach ($order->items as $orderItem) {
                $cart->cart[$order->table_id]['items'][$orderItem->cart_id] = [
                    "product_id"   => $orderItem->product_id,
                    "name"         => $orderItem->product_name,
                    "quantity"     => $orderItem->quantity,
                    "unit_price"   => formatAmount($orderItem->unit_cost, currency_symbol(request()->activeBusiness->currency)),
                    "raw_price"    => $orderItem->unit_cost,
                    "total_price"  => $orderItem->unit_cost * $orderItem->quantity,
                    "image"        => $orderItem->product->image,
                    "attributes"   => null,
                    "add_on_items" => null,
                    "product"      => $orderItem->product->toArray(),
                    "table_id"     => $order->table_id,
                ];
            }
            session()->put('cart', $cart->cart);
        }

    }

    public static function findByOrderId($orderId) {
        if (session()->has('cart')) {
            $carts  = session()->get('cart');
            $result = collect($carts)->where('order_id', $orderId);
            return $result->toArray();
        }
        return null;
    }

    public function addItem($cartId, $item = array()) {
        if (!isset($this->cart[$this->tableId]['items'][$cartId])) {
            $this->cart[$this->tableId]['items'][$cartId] = $item;
        } else {
            $this->cart[$this->tableId]['items'][$cartId]['quantity']    = $this->cart[$this->tableId]['items'][$cartId]['quantity'] + $item['quantity'];
            $this->cart[$this->tableId]['items'][$cartId]['total_price'] = $this->cart[$this->tableId]['items'][$cartId]['raw_price'] * $this->cart[$this->tableId]['items'][$cartId]['quantity'];
        }
        $this->needUpdate();
        $this->calculateSummary();
    }

    public function updateQuantity($cartId, $quantity) {
        $this->cart[$this->tableId]['items'][$cartId]['quantity']    = $quantity;
        $this->cart[$this->tableId]['items'][$cartId]['total_price'] = $this->cart[$this->tableId]['items'][$cartId]['raw_price'] * $this->cart[$this->tableId]['items'][$cartId]['quantity'];
        $this->needUpdate();
        $this->calculateSummary();
    }

    public function removeCart($cartId) {
        unset($this->cart[$this->tableId]['items'][$cartId]);
        $this->needUpdate();
        $this->calculateSummary();
    }

    public function addDiscount($discount) {
        $this->cart[$this->tableId]['discount']['percentage'] = $discount;
        $this->cart[$this->tableId]['discount']['amount']     = formatAmount(($discount / 100) * $this->subTotal, currency_symbol($this->currency));
        $this->cart[$this->tableId]['discount']['rawAmount']  = ($discount / 100) * $this->subTotal;
        $this->needUpdate();
        $this->calculateSummary();
    }

    public function applyTax($taxes) {
        $sessionTaxes = [];
        if (isset($taxes)) {
            $taxes = Tax::whereIn('id', $taxes)->get();
            foreach ($taxes as $tax) {
                $temp = [
                    'id'        => $tax->id,
                    'name'      => $tax->name,
                    'rate'      => $tax->rate,
                    'amount'    => formatAmount(($tax->rate / 100) * $this->subTotal, currency_symbol($this->currency)),
                    'rawAmount' => ($tax->rate / 100) * $this->subTotal,
                ];
                array_push($sessionTaxes, $temp);
            }
        }
        $this->cart[$this->tableId]['taxes'] = $sessionTaxes;
        $this->needUpdate();

        $this->calculateSummary();
    }

    public function applyDefaultTax() {
        $sessionTaxes = [];
        $taxes        = Tax::active()->get();

        foreach ($taxes as $tax) {
            $temp = [
                'id'        => $tax->id,
                'name'      => $tax->name,
                'rate'      => $tax->rate,
                'amount'    => formatAmount(($tax->rate / 100) * $this->subTotal, currency_symbol($this->currency)),
                'rawAmount' => ($tax->rate / 100) * $this->subTotal,
            ];
            array_push($sessionTaxes, $temp);
        }

        $this->cart[$this->tableId]['taxes'] = $sessionTaxes;
        $this->needUpdate();
        $this->calculateSummary();
    }

    public function updateOrderStatus($status, $order_id, $unset = true) {
        $this->cart[$this->tableId]['order_status'] = $status;
        $this->cart[$this->tableId]['order_id']     = $order_id;

        if ($status == 5 && $unset == true) {
            unset($this->cart[$this->tableId]);
        }
        session()->put('cart', $this->cart);
    }

    public function needUpdate($update = true) {
        $this->cart[$this->tableId]['need_update'] = $update;
        session()->put('cart', $this->cart);
    }

    public function addOrderNumber($order_number) {
        $this->cart[$this->tableId]['order_number'] = $order_number;
        session()->put('cart', $this->cart);
    }

    public function emptyCart() {
        unset($this->cart[$this->tableId]);
        session()->put('cart', $this->cart);
    }

    public function getItems() {
        return collect($this->cart[$this->tableId]['items']);
    }

    public function getTaxes() {
        return collect($this->cart[$this->tableId]['taxes']);
    }

    public function getDiscount($column = null) {
        if ($column != null) {
            return $this->discount[$column];
        }
        return $this->discount;
    }

    public function getServiceCharge($column = null) {
        if ($column != null) {
            return $this->serviceCharge[$column];
        }
        return $this->serviceCharge;
    }

    public function getSubTotal($currency = false) {
        if ($currency == true) {
            return formatAmount($this->subTotal, currency_symbol($this->currency));
        }
        return $this->subTotal;
    }

    public function getGrandTotal($currency = false) {
        if ($currency == true) {
            return formatAmount($this->grandTotal, currency_symbol($this->currency));
        }
        return $this->grandTotal;
    }

    public function getOrderStatus($html = false) {
        $status = $this->cart[$this->tableId]['order_status'];
        if ($html == true) {
            return order_status($status);
        }
        return $status;
    }

    public function getOrderId() {
        return $this->cart[$this->tableId]['order_id'];
    }

    public function getOrderNumber() {
        return $this->cart[$this->tableId]['order_number'];
    }

    public function getOrderType() {
        return $this->cart[$this->tableId]['order_type'];
    }

    public function getNeedUpdate() {
        if ($this->getOrderId() != null) {
            return $this->cart[$this->tableId]['need_update'];
        }
        return false;
    }

    private function calculateSummary() {
        $this->currency = request()->activeBusiness->currency;
        $this->subTotal = collect($this->cart[$this->tableId]['items'])->sum('total_price');

        //Update Discount
        $this->cart[$this->tableId]['discount']['amount']    = formatAmount(($this->cart[$this->tableId]['discount']['percentage'] / 100) * $this->subTotal, currency_symbol($this->currency));
        $this->cart[$this->tableId]['discount']['rawAmount'] = ($this->cart[$this->tableId]['discount']['percentage'] / 100) * $this->subTotal;

        //Update TAX
        if (!empty($this->cart[$this->tableId]['taxes'])) {
            $sessionTaxes = [];
            foreach ($this->cart[$this->tableId]['taxes'] as $tax) {
                $temp = [
                    'id'        => $tax['id'],
                    'name'      => $tax['name'],
                    'rate'      => $tax['rate'],
                    'amount'    => formatAmount(($tax['rate'] / 100) * $this->subTotal, currency_symbol($this->currency)),
                    'rawAmount' => ($tax['rate'] / 100) * $this->subTotal,
                ];
                array_push($sessionTaxes, $temp);
            }
            $this->cart[$this->tableId]['taxes'] = $sessionTaxes;
        }

        //Add Service Charge
        $serviceChargePercentage = get_business_option('service_charge', 0, request()->activeBusiness->id);
        if ($serviceChargePercentage > 0) {
            $this->cart[$this->tableId]['serviceCharge']['percentage'] = $serviceChargePercentage;
            $this->cart[$this->tableId]['serviceCharge']['rawAmount']  = ($serviceChargePercentage / 100) * $this->subTotal;
            $this->cart[$this->tableId]['serviceCharge']['amount']     = formatAmount(($serviceChargePercentage / 100) * $this->subTotal, currency_symbol($this->currency));
        }
        $this->discount      = $this->cart[$this->tableId]['discount'];
        $this->serviceCharge = $this->cart[$this->tableId]['serviceCharge'];
        $this->taxes         = collect($this->cart[$this->tableId]['taxes']);
        $this->grandTotal    = ($this->subTotal + $this->taxes->sum('rawAmount') + $this->serviceCharge['rawAmount']) - $this->discount['rawAmount'];

        session()->put('cart', $this->cart);
    }

}