<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchaseTax;
use App\Models\Tax;
use App\Models\Transaction;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class PurchaseController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $assets = ['datatable'];
        return view('backend.user.purchase.list', compact('assets'));
    }

    public function get_table_data(Request $request) {
        $purchases = Purchase::select('purchases.*')
            ->with('vendor')
            ->orderBy("purchases.id", "desc");

        return Datatables::eloquent($purchases)
            ->editColumn('grand_total', function ($purchase) {
                return '<div class="text-right">' . formatAmount($purchase->grand_total, currency_symbol(request()->activeBusiness->currency)) . '</div>';
            })
            ->addColumn('amount_due', function ($purchase) {
                return '<div class="text-right">' . formatAmount($purchase->grand_total - $purchase->paid, currency_symbol(request()->activeBusiness->currency)) . '</div>';
            })
            ->editColumn('status', function ($purchase) {
                return '<div class="text-center">' . purchase_status($purchase) . '</div>';
            })
            ->addColumn('action', function ($purchase) {
                return '<div class="dropdown text-center">'
                . '<button class="btn btn-outline-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">' . _lang('Action')
                . '</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item ' . ($purchase->status == 2 ? "disabled" : "") . '" href="' . ($purchase->status != 2 ? route('purchases.edit', $purchase['id']) : '') . '"><i class="far fa-edit mr-2"></i>' . _lang('Edit') . '</a>'
                . '<a class="dropdown-item" href="' . route('purchases.show', $purchase['id']) . '"><i class="far fa-eye mr-2"></i>' . _lang('Preview') . '</a>'
                . '<a class="dropdown-item" href="' . route('purchases.duplicate', $purchase['id']) . '"><i class="far fa-copy mr-2"></i>' . _lang('Duplicate') . '</a>'
                . '<a class="dropdown-item ajax-modal" href="' . route('purchases.add_payment', $purchase['id']) . '" data-title="' . _lang('Add Payment') . '"><i class="far fa-credit-card mr-2"></i>' . _lang('Add Payment') . '</a>'
                . '<form action="' . route('purchases.destroy', $purchase['id']) . '" method="post">'
                . csrf_field()
                . '<input name="_method" type="hidden" value="DELETE">'
                . ' <div class="dropdown-divider"></div>'
                . '<button class="dropdown-item btn-remove" type="submit"><i class="fas fa-minus-circle mr-2"></i>' . _lang('Delete') . '</button>'
                    . '</form>'
                    . '</div>'
                    . '</div>';
            })
            ->filter(function ($query) use ($request) {
                if ($request->has('bill_no')) {
                    $query->where('bill_no', 'like', "%{$request->bill_no}%");
                }

                if ($request->has('vendor_id')) {
                    $query->where('vendor_id', $request->vendor_id);
                }

                if ($request->has('status')) {
                    $query->whereIn('status', json_decode($request->status));
                }

                if ($request->has('date_range')) {
                    $date_range = explode(" - ", $request->date_range);
                    $query->whereBetween('invoice_date', [$date_range[0], $date_range[1]]);
                }
            })
            ->setRowId(function ($purchase) {
                return "row_" . $purchase->id;
            })
            ->rawColumns(['grand_total', 'amount_due', 'status', 'action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        return view('backend.user.purchase.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'vendor_id'     => 'required',
            'title'         => 'required',
            'bill_no'       => 'required',
            'purchase_date' => 'required|date',
            'due_date'      => 'required|after_or_equal:purchase_date',
            'product_id'    => 'required',
        ], [
            'product_id.required' => _lang('You must add at least one item'),
        ]);

        if ($validator->fails()) {
            return redirect()->route('invoices.create')
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        $summary = $this->calculateTotal($request);

        $purchase                 = new Purchase();
        $purchase->vendor_id      = $request->input('vendor_id');
        $purchase->title          = $request->input('title');
        $purchase->bill_no        = $request->input('bill_no');
        $purchase->po_so_number   = $request->input('po_so_number');
        $purchase->purchase_date  = $request->input('purchase_date');
        $purchase->due_date       = $request->input('due_date');
        $purchase->sub_total      = $summary['subTotal'];
        $purchase->grand_total    = $summary['grandTotal'];
        $purchase->paid           = 0;
        $purchase->discount       = $summary['discountAmount'];
        $purchase->discount_type  = $request->input('discount_type');
        $purchase->discount_value = $request->input('discount_value');
        $purchase->template_type  = 0;
        $purchase->template       = $request->input('template');
        $purchase->note           = $request->input('note');
        $purchase->footer         = $request->input('footer');
        $purchase->short_code     = rand(100000, 9999999) . uniqid();

        $purchase->save();

        for ($i = 0; $i < count($request->product_id); $i++) {
            $purchaseItem = $purchase->items()->save(new PurchaseItem([
                'purchase_id'         => $purchase->id,
                'purchase_product_id' => $request->product_id[$i],
                'product_name'        => $request->product_name[$i],
                'description'         => $request->description[$i],
                'quantity'            => $request->quantity[$i],
                'unit_cost'           => $request->unit_cost[$i],
                'sub_total'           => ($request->unit_cost[$i] * $request->quantity[$i]),
            ]));

            //Update Stock
            $product = $purchaseItem->product;
            if ($product->stock_management == 1) {
                $product->stock = $product->stock + $request->quantity[$i];
                $product->save();
            }
        }

        if (isset($request->taxes)) {
            foreach ($request->taxes as $taxId) {
                $tax = Tax::find($taxId);

                $purchase->taxes()->save(new PurchaseTax([
                    'purchase_id' => $purchase->id,
                    'tax_id'      => $taxId,
                    'name'        => $tax->name . ' ' . $tax->rate . ' %',
                    'amount'      => ($purchase->sub_total / 100) * $tax->rate,
                ]));
            }
        }

        DB::commit();

        if ($purchase->id > 0) {
            return redirect()->route('purchases.show', $purchase->id)->with('success', _lang('Saved Successfully'));
        } else {
            return back()->with('error', _lang('Something going wrong, Please try again'));
        }

    }

    /**
     * Preview Private Invoice
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id) {
        $alert_col = 'col-lg-8 offset-lg-2';
        $purchase  = Purchase::with(['business', 'items'])->find($id);
        return view('backend.user.purchase.view', compact('purchase', 'id', 'alert_col'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id) {
        $purchase = Purchase::with('items')
            ->where('id', $id)
            ->where('status', '!=', 2)
            ->first();
        return view('backend.user.purchase.edit', compact('purchase', 'id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'vendor_id'     => 'required',
            'title'         => 'required',
            'bill_no'       => 'required',
            'purchase_date' => 'required|date',
            'due_date'      => 'required|date|after_or_equal:purchase_date',
            'product_id'    => 'required',
        ], [
            'product_id.required' => _lang('You must add at least one item'),
        ]);

        if ($validator->fails()) {
            return redirect()->route('purchases.edit', $id)
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        $summary = $this->calculateTotal($request);

        $purchase = Purchase::where('id', $id)
            ->where('status', '!=', 2)
            ->first();
        $purchase->vendor_id      = $request->input('vendor_id');
        $purchase->title          = $request->input('title');
        $purchase->bill_no        = $request->input('bill_no');
        $purchase->po_so_number   = $request->input('po_so_number');
        $purchase->purchase_date  = $request->input('purchase_date');
        $purchase->due_date       = $request->input('due_date');
        $purchase->sub_total      = $summary['subTotal'];
        $purchase->grand_total    = $summary['grandTotal'];
        $purchase->discount       = $summary['discountAmount'];
        $purchase->discount_type  = $request->input('discount_type');
        $purchase->discount_value = $request->input('discount_value');
        $purchase->template_type  = 0;
        $purchase->template       = $request->input('template');
        $purchase->note           = $request->input('note');
        $purchase->footer         = $request->input('footer');

        $purchase->save();

        //Update Invoice item
        foreach ($purchase->items as $purchase_item) {
            $product = $purchase_item->product;
            if ($product->stock_management == 1) {
                $product->stock = $product->stock - $purchase_item->quantity;
                $product->save();
            }
            $purchase_item->delete();
        }

        for ($i = 0; $i < count($request->product_id); $i++) {
            $purchaseItem = $purchase->items()->save(new PurchaseItem([
                'purchase_id'         => $purchase->id,
                'purchase_product_id' => $request->product_id[$i],
                'product_name'        => $request->product_name[$i],
                'description'         => $request->description[$i],
                'quantity'            => $request->quantity[$i],
                'unit_cost'           => $request->unit_cost[$i],
                'sub_total'           => ($request->unit_cost[$i] * $request->quantity[$i]),
            ]));

            //Update Stock
            $product = $purchaseItem->product;
            if ($product->stock_management == 1) {
                $product->stock = $product->stock + $request->quantity[$i];
                $product->save();
            }
        }

        if (isset($request->taxes)) {
            $purchase->taxes()->delete();
            foreach ($request->taxes as $taxId) {
                $tax = Tax::find($taxId);

                $purchase->taxes()->save(new PurchaseTax([
                    'purchase_id' => $purchase->id,
                    'tax_id'      => $taxId,
                    'name'        => $tax->name . ' ' . $tax->rate . ' %',
                    'amount'      => ($purchase->sub_total / 100) * $tax->rate,
                ]));
            }
        }

        DB::commit();

        if (!$request->ajax()) {
            if ($purchase->status == 0) {
                return redirect()->route('purchases.show', $purchase->id)->with('success', _lang('Updated Successfully'));
            } else {
                return redirect()->route('purchases.index')->with('success', _lang('Updated Successfully'));
            }
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Updated Successfully'), 'data' => $invoice, 'table' => '#invoices_table']);
        }

    }

    /** Duplicate Invoice */
    public function duplicate($id) {
        DB::beginTransaction();
        $purchase                = Purchase::find($id);
        $newPurchase             = $purchase->replicate();
        $newPurchase->status     = 0;
        $newPurchase->paid       = 0;
        $newPurchase->short_code = rand(100000, 9999999) . uniqid();
        $newPurchase->save();

        foreach ($purchase->items as $purchaseItem) {
            $newPurchaseItem              = $purchaseItem->replicate();
            $newPurchaseItem->purchase_id = $newPurchase->id;
            $newPurchaseItem->save();

            //Update Stock
            $product = $purchaseItem->product;
            if ($product->stock_management == 1) {
                $product->stock = $product->stock + $newPurchaseItem->quantity;
                $product->save();
            }
        }

		foreach ($purchase->taxes as $purchaseTax) {
			$newPurchaseTax                   = $purchaseTax->replicate();
			$newPurchaseTax->purchase_id      = $newPurchase->id;
			$newPurchaseTax->save();
		}

        DB::commit();

        return redirect()->route('purchases.edit', $newPurchase->id);
    }

    public function add_payment(Request $request, $id) {
        if (!$request->ajax()) {
            return back();
        }
        if ($request->isMethod('get')) {
            $purchase = Purchase::find($id);
            return view('backend.user.purchase.modal.add-payment', compact('purchase'));
        } else if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'trans_date' => 'required',
                'method'     => 'required',
                'amount'     => 'required|numeric',
                'attachment' => 'nullable|mimes:jpeg,JPEG,png,PNG,jpg,doc,pdf,docx,zip',
            ]);

            if ($validator->fails()) {
                if ($request->ajax()) {
                    return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
                }
            }

            DB::beginTransaction();

            $purchase = Purchase::find($id);

            if ($request->amount > ($purchase->grand_total - $purchase->paid)) {
                return response()->json(['result' => 'error', 'message' => _lang('Amount must be equal or less than due amount')]);
            }

            $attachment = '';
            if ($request->hasfile('attachment')) {
                $file       = $request->file('attachment');
                $attachment = rand() . time() . $file->getClientOriginalName();
                $file->move(public_path() . "/uploads/media/", $attachment);
            }

            $transaction              = new Transaction();
            $transaction->trans_date  = $request->input('trans_date');
            $transaction->method      = $request->method;
            $transaction->dr_cr       = 'dr';
            $transaction->type        = 'expense';
            $transaction->amount      = $request->input('amount');
            $transaction->reference   = $request->input('reference');
            $transaction->description = _lang('Purchase / Bill') . ' #' . $purchase->bill_no;
            $transaction->attachment  = $attachment;
            $transaction->ref_id      = $purchase->id;
            $transaction->ref_type    = 'purchase';

            $transaction->save();

            $purchase->paid   = $purchase->paid + $transaction->amount;
            $purchase->status = 1; //Partially Paid
            if ($purchase->paid >= $purchase->grand_total) {
                $purchase->status = 2; //Paid
            }
            $purchase->save();

            DB::commit();

            if ($transaction->id > 0) {
                return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Payment made successfully'), 'data' => $transaction]);
            } else {
                return response()->json(['result' => 'error', 'message' => _lang('Error occured, please try again')]);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $purchase = Purchase::find($id);
        $purchase->delete();
        return redirect()->route('purchases.index')->with('success', _lang('Deleted Successfully'));
    }

    private function calculateTotal(Request $request) {
        $subTotal       = 0;
        $totalTax       = 0;
        $discountAmount = 0;
        $grandTotal     = 0;

        for ($i = 0; $i < count($request->product_id); $i++) {
            //Calculate Sub Total
            $line_qnt       = $request->quantity[$i];
            $line_unit_cost = $request->unit_cost[$i];
            $line_total     = ($line_qnt * $line_unit_cost);

            //Show Sub Total
            $subTotal = ($subTotal + $line_total);
        }

        //Calculate Discount
        if ($request->discount_type == '0') {
            $discountAmount = ($subTotal / 100) * $request->discount_value;
        } else if ($request->discount_type == '1') {
            $discountAmount = $request->discount_value;
        }

        //Calculate Taxes
        if (isset($request->taxes)) {
            for ($j = 0; $j < count($request->taxes); $j++) {
                $taxId      = $request->taxes[$j];
                $tax        = Tax::find($taxId);
                $tax_amount = ($subTotal / 100) * $tax->rate;
                $totalTax += $tax_amount;
            }
        }

        //Calculate Grand Total
        $grandTotal = ($subTotal + $totalTax) - $discountAmount;

        return array(
            'subTotal'       => $subTotal,
            'taxAmount'      => $totalTax,
            'discountAmount' => $discountAmount,
            'grandTotal'     => $grandTotal,
        );

    }
}