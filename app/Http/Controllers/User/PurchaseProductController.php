<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PurchaseProduct;
use DataTables;
use Illuminate\Http\Request;
use Validator;

class PurchaseProductController extends Controller {

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
        return view('backend.user.purchase_item.list', compact('assets'));
    }

    public function get_table_data() {
        $purchaseitems = PurchaseProduct::select('purchase_products.*')
            ->orderBy("purchase_products.id", "desc");

        return Datatables::eloquent($purchaseitems)
            ->editColumn('image', function ($product) {
                return '<img src="' . asset('public/uploads/media/' . $product->image) . '" class="thumb-sm img-thumbnail">';
            })
            ->editColumn('purchase_cost', function ($product) {
                return '<div class="text-right">' . formatAmount($product->purchase_cost, currency_symbol(request()->activeBusiness->currency)) . '</div>';
            })
            ->editColumn('stock', function ($product) {
                return '<div class="text-center">' . $product->stock . ' ' . $product->measurement_unit . '</div>';
            })
            ->editColumn('status', function ($product) {
                return '<div class="text-center">' . status($product->status) . '</div>';
            })
            ->addColumn('action', function ($purchaseitem) {
                return '<div class="dropdown text-center">'
                . '<button class="btn btn-outline-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">' . _lang('Action')
                . '</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item" href="' . route('purchase_items.edit', $purchaseitem['id']) . '" data-title="' . _lang('Update Purchase Item') . '"><i class="fas fa-pencil-alt"></i> ' . _lang('Edit') . '</a>'
                . '<form action="' . route('purchase_items.destroy', $purchaseitem['id']) . '" method="post">'
                . csrf_field()
                . '<input name="_method" type="hidden" value="DELETE">'
                . '<button class="dropdown-item btn-remove" type="submit"><i class="fas fa-trash-alt"></i> ' . _lang('Delete') . '</button>'
                    . '</form>'
                    . '</div>'
                    . '</div>';
            })
            ->setRowId(function ($purchaseitem) {
                return "row_" . $purchaseitem->id;
            })
            ->rawColumns(['image', 'purchase_cost', 'stock', 'status', 'action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        $alert_col = 'col-lg-8 offset-lg-2';
        if (!$request->ajax()) {
            return view('backend.user.purchase_item.create', compact('alert_col'));
        }else{
            return view('backend.user.purchase_item.modal.create');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name'             => 'required',
            'measurement_unit' => 'required',
            'purchase_cost'    => 'required|numeric',
            'image'            => 'nullable|image|max:2048',
            'status'           => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('purchase_items.create')
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $image = 'default.png';
        if ($request->hasfile('image')) {
            $file  = $request->file('image');
            $image = time() . $file->getClientOriginalName();
            $file->move(public_path() . "/uploads/media/", $image);
        }

        $purchaseitem                   = new PurchaseProduct();
        $purchaseitem->name             = $request->input('name');
        $purchaseitem->measurement_unit = $request->input('measurement_unit');
        $purchaseitem->purchase_cost    = $request->input('purchase_cost');
        $purchaseitem->image            = $image;
        $purchaseitem->descriptions     = $request->input('descriptions');
        $purchaseitem->status           = $request->input('status');
        $purchaseitem->user_id          = $request->input('user_id');
        $purchaseitem->business_id      = $request->input('business_id');
        $purchaseitem->created_user_id  = $request->input('created_user_id');
        $purchaseitem->updated_user_id  = $request->input('updated_user_id');

        $purchaseitem->save();

        if (!$request->ajax()) {
            return redirect()->route('purchase_items.index')->with('success', _lang('Saved Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Saved Successfully'), 'data' => $purchaseitem, 'table' => '#purchase_items_table']);
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id) {
        $alert_col    = 'col-lg-8 offset-lg-2';
        $purchaseitem = PurchaseProduct::find($id);
        return view('backend.user.purchase_item.edit', compact('purchaseitem', 'id', 'alert_col'));
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
            'name'             => 'required',
            'measurement_unit' => 'required',
            'purchase_cost'    => 'required|numeric',
            'image'            => 'nullable|image|max:2048',
            'status'           => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('purchase_items.edit', $id)
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        if ($request->hasfile('image')) {
            $file  = $request->file('image');
            $image = time() . $file->getClientOriginalName();
            $file->move(public_path() . "/uploads/media/", $image);
        }

        $purchaseitem                   = PurchaseProduct::find($id);
        $purchaseitem->name             = $request->input('name');
        $purchaseitem->measurement_unit = $request->input('measurement_unit');
        $purchaseitem->purchase_cost    = $request->input('purchase_cost');
        if ($request->hasfile('image')) {
            $purchaseitem->image = $image;
        }
        $purchaseitem->descriptions     = $request->input('descriptions');
        $purchaseitem->status           = $request->input('status');
        $purchaseitem->user_id          = $request->input('user_id');
        $purchaseitem->business_id      = $request->input('business_id');
        $purchaseitem->created_user_id  = $request->input('created_user_id');
        $purchaseitem->updated_user_id  = $request->input('updated_user_id');

        $purchaseitem->save();

        if (!$request->ajax()) {
            return redirect()->route('purchase_items.index')->with('success', _lang('Updated Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Updated Successfully'), 'data' => $purchaseitem, 'table' => '#purchase_items_table']);
        }

    }

    public function get_product($id) {
        $product       = PurchaseProduct::active()->find($id);
        $decimal_place = get_business_option('decimal_places', 2);
        return response()->json(array('product' => $product, 'decimal_place' => $decimal_place));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $purchaseitem = PurchaseProduct::find($id);
        $purchaseitem->delete();
        return redirect()->route('purchase_items.index')->with('success', _lang('Deleted Successfully'));
    }
}