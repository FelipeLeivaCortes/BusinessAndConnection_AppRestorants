<?php

namespace App\Http\Controllers\User;

use Validator;
use DataTables;
use App\Models\ProductModel as Product;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller {

     /**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->middleware(function ($request, $next) {

			$route_name = request()->route()->getName();
			if ($route_name == 'products.store') {
				if (has_limit('products', 'item_limit', true) <= 0) {
					if (!$request->ajax()) {
						return back()->with('error', _lang('Sorry, Your have already reached your package quota !'));
					} else {
						return response()->json(['result' => 'error', 'message' => _lang('Sorry, Your have already reached your package quota !')]);
					}
				}
			}

			return $next($request);
		});
	}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $assets = ['datatable'];
        return view('backend.user.product.list', compact('assets'));
    }

    public function get_table_data() {
        $products = Product::select('products.*')
            ->with('category');

        return Datatables::eloquent($products)
            ->editColumn('image', function ($product) {
                return '<img src="' . asset('public/uploads/media/' . $product->image) . '" class="thumb-sm img-thumbnail">';
            })
            ->editColumn('price', function ($product) {
                return '<div class="text-left">' . formatAmount($product->price, currency_symbol(request()->activeBusiness->currency)) . '</div>';
            })
            ->editColumn('special_price', function ($product) {
                return '<div class="text-left">' . formatAmount($product->special_price, currency_symbol(request()->activeBusiness->currency)) . '</div>';
            })
            ->editColumn('status', function ($product) {
                return '<div class="text-center">' . status($product->status) . '</div>';
            })
            ->addColumn('action', function ($product) {
                return '<div class="dropdown text-center">'
                . '<button class="btn btn-outline-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">' . _lang('Action')
                . '</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item" href="' . route('products.edit', $product['id']) . '"><i class="fas fa-pencil-alt"></i> ' . _lang('Edit') . '</a>'
                . '<a class="dropdown-item" href="' . route('product_addons.index', $product['id']) . '"><i class="fas fa-plus-circle"></i> ' . _lang('Addons Items') . '</a>'
                . '<form action="' . route('products.destroy', $product['id']) . '" method="post">'
                . csrf_field()
                . '<input name="_method" type="hidden" value="DELETE">'
                . '<button class="dropdown-item btn-remove" type="submit"><i class="fas fa-trash-alt"></i> ' . _lang('Delete') . '</button>'
                    . '</form>'
                    . '</div>'
                    . '</div>';
            })
            ->setRowId(function ($product) {
                return "row_" . $product->id;
            })
            ->rawColumns(['image', 'price', 'special_price', 'status', 'action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        $alert_col = 'col-lg-10 offset-lg-1';
        return view('backend.user.product.create', compact('alert_col'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name'          => 'required',
            'category_id'   => 'required',
            'image'         => 'nullable|image|max:2048',
            'price'         => 'required|numeric',
            'special_price' => 'nullable|numeric',
            'status'        => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->route('products.create')
                ->withErrors($validator)
                ->withInput();
        }

        $image = 'default.png';
        if ($request->hasfile('image')) {
            $file  = $request->file('image');
            $image = time() . $file->getClientOriginalName();
            Image::make($file)->crop(400, 400)->save(public_path() . "/uploads/media/" . $image);
        }

        DB::beginTransaction();

        $product                = new Product();
        $product->name          = $request->input('name');
        $product->category_id   = $request->input('category_id');
        $product->image         = $image;
        $product->price         = $request->input('price');
        $product->special_price = $request->input('special_price');
        $product->description   = $request->input('description');
        $product->product_type  = $request->input('product_type');
        $product->status        = $request->input('status');
        
        $product->save();

        //Store Product variations
        if ($product->product_type == '2') {

            if (isset($request->product_option)) {
                $i = 0;
                foreach ($request->product_option as $product_option) {
                    $variation = $product->product_options()->create(['name' => $product_option]);

                    //Store Product value
                    if (isset($request->product_option_value[$i])) {
                        foreach (explode(',', $request->product_option_value[$i]) as $product_option_value) {
                            $variation->items()->create(['name' => $product_option_value]);
                        }
                    }
                    $i++;
                }
            }

            //Store Variations Price
            $variations = array();

            foreach ($product->product_options as $product_option) {
                $variations[$product_option->id] = $product_option->items;
            }

            $i = 0;
            foreach (cartesian($variations) as $variation) {
                $data                  = array();
                $data['option']        = json_encode($variation);
                $data['price']         = isset($request->variation_price[$i]) ? $request->variation_price[$i] : $request->price;
                $data['special_price'] = isset($request->variation_special_price[$i]) ? $request->variation_special_price[$i] : $request->special_price;
                $data['is_available']  = isset($request->is_available[$i]) ? $request->is_available[$i] : 0;
                $product->variation_prices()->create($data);

                $i++;
            }
        }

        DB::commit();

        return redirect()->route('products.create')->with('success', _lang('Saved Successfully'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id) {
        $alert_col = 'col-lg-10 offset-lg-1';
        $product   = Product::find($id);
        return view('backend.user.product.edit', compact('product', 'id', 'alert_col'));
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
            'name'          => 'required',
            'category_id'   => 'required',
            'image'         => 'nullable|image|max:2048',
            'price'         => 'required|numeric',
            'special_price' => 'nullable|numeric',
            'status'        => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->route('products.edit', $id)
                ->withErrors($validator)
                ->withInput();
        }

        if ($request->hasfile('image')) {
            $file  = $request->file('image');
            $image = time() . $file->getClientOriginalName();
            Image::make($file)->crop(400, 400)->save(public_path() . "/uploads/media/" . $image);
        }

        DB::beginTransaction();

        $product              = Product::find($id);
        $product->name        = $request->input('name');
        $product->category_id = $request->input('category_id');
        if ($request->hasfile('image')) {
            $product->image = $image;
        }
        $product->price         = $request->input('price');
        $product->special_price = $request->input('special_price');
        $product->description   = $request->input('description');
        $product->product_type  = $request->input('product_type');
        $product->status        = $request->input('status');

        $product->save();

        //Store Product variations
        if ($product->product_type == '2' && $request->update_variation == 1) {

            //Remove Previous Data
            $product->product_options()->delete();
            $product->variation_prices()->delete();

            if (isset($request->product_option)) {
                $i = 0;
                foreach ($request->product_option as $product_option) {
                    $variation = $product->product_options()->create(['name' => $product_option]);

                    //Store Variation Items
                    if (isset($request->product_option_value[$i])) {
                        foreach (explode(',', $request->product_option_value[$i]) as $product_option_value) {
                            $variation->items()->create(['name' => $product_option_value]);
                        }
                    }

                    $i++;
                }
            }

            //Store Variations Price
            $variations = array();

            foreach ($product->product_options as $product_option) {
                $variations[$product_option->id] = $product_option->items;
            }

            $i = 0;
            foreach (cartesian($variations) as $variation) {
                $data                  = array();
                $data['option']        = json_encode($variation);
                $data['price']         = isset($request->variation_price[$i]) ? $request->variation_price[$i] : $request->price;
                $data['special_price'] = isset($request->variation_special_price[$i]) ? $request->variation_special_price[$i] : $request->special_price;
                $data['is_available']  = isset($request->is_available[$i]) ? $request->is_available[$i] : 0;
                $product->variation_prices()->create($data);

                $i++;
            }
        }

        DB::commit();

        return redirect()->route('products.index')->with('success', _lang('Updated Successfully'));
    }

    public function generate_variations(Request $request) {
        $variations = array();

        $option_values = explode('&', $request->product_option_value[0]);

        $index = 0;
        foreach (explode('&', $request->product_option[0]) as $product_option) {
            $option_value       = explode('=', $option_values[$index]);
            $variations[$index] = explode('%2C', $option_value[1]);
            $index++;
        }

        echo json_encode(cartesian($variations));
    }

    /** Get variation price **/
    public function get_variation_price(Request $request, $product_id) {
        //Get Variation Product Price
        $variation = get_variation_price($request->product_option, $product_id);
        $price = $variation['price'];
        $is_available = $variation['is_available'];

        //Get Addon Product Price
        $addOnProducts = get_addon_price($request->product_addon_id, $product_id);
        $add_on_prices = $addOnProducts['price'];

        if ($price == 0) {
            if (Cache::has('products_list'.business_id())) {
                $products = Cache::get('products_list'.business_id());
                $product  = $products->find($product_id);
            } else {
                $product = Product::find($product_id);
            }
            $price = $product->special_price > 0 ? $product->special_price : $product->price;
        }

        echo json_encode(
            array(
                'result'        => true,
                'price'         => formatAmount($price + $add_on_prices, currency_symbol($request->activeBusiness->currency)),
                'is_available'  => $is_available,
            )
        );

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $product = Product::find($id);
        $product->delete();
        return redirect()->route('products.index')->with('success', _lang('Deleted Successfully'));
    }
}