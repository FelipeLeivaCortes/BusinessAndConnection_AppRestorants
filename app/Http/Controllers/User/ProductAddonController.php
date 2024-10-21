<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ProductModel as Product;
use App\Models\ProductAddon;
use Illuminate\Http\Request;
use Validator;

class ProductAddonController extends Controller {

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
    public function index($productId) {
        $assets        = ['datatable'];
        $product       = Product::find($productId);
        $productaddons = ProductAddon::where('product_id', $productId)->get();
        return view('backend.user.product.product_addon.list', compact('productaddons', 'assets', 'productId', 'product'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $productId) {
        if (!$request->ajax()) {
            return back();
        } else {
            return view('backend.user.product.product_addon.modal.create', compact('productId'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $productId) {
        $validator = Validator::make($request->all(), [
            'name'  => 'required',
            'price' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $productaddon              = new ProductAddon();
        $productaddon->product_id  = $productId;
        $productaddon->name        = $request->input('name');
        $productaddon->price       = $request->input('price');
        $productaddon->description = $request->input('description');

        $productaddon->save();

        $productaddon->price = formatAmount($productaddon->price, currency_symbol(request()->activeBusiness->currency));

        if (!$request->ajax()) {
            return redirect()->route('product_addons.create')->with('success', _lang('Saved Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Saved Successfully'), 'data' => $productaddon, 'table' => '#product_addons_table']);
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id) {
        $productaddon = ProductAddon::find($id);
        if (!$request->ajax()) {
            return back();
        } else {
            return view('backend.user.product.product_addon.modal.edit', compact('productaddon', 'id'));
        }

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
            'name'  => 'required',
            'price' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->back()->withErrors($validator)
                    ->withInput();
            }
        }

        $productaddon              = ProductAddon::find($id);
        $productaddon->name        = $request->input('name');
        $productaddon->price       = $request->input('price');
        $productaddon->description = $request->input('description');

        $productaddon->save();
        $productaddon->price = formatAmount($productaddon->price, currency_symbol(request()->activeBusiness->currency));

        if (!$request->ajax()) {
            return redirect()->route('product_addons.index')->with('success', _lang('Updated Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Updated Successfully'), 'data' => $productaddon, 'table' => '#product_addons_table']);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $productaddon = ProductAddon::find($id);
        $productaddon->delete();
        return back()->with('success', _lang('Deleted Successfully'));
    }
}
