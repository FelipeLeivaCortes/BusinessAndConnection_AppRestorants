<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\CategoryModel as Category;
use Illuminate\Http\Request;
use Validator;

class CategoryController extends Controller {

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
        $assets     = ['datatable'];
        $categories = Category::all()->sortBy("position");
        return view('backend.user.category.list', compact('categories', 'assets'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        $max_position = Category::count() + 1;
        if (!$request->ajax()) {
            return back();
        } else {
            return view('backend.user.category.modal.create', compact('max_position'));
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
            'name'     => 'required',
            'status'   => 'required',
            'position' => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('categories.create')
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $category              = new Category();
        $category->name        = $request->input('name');
        $category->description = $request->input('description');
        $category->status      = $request->input('status');
        $category->position    = $request->input('position');

        $category->save();
        $category->status = status($category->status);

        if (!$request->ajax()) {
            return redirect()->route('categories.list')->with('success', _lang('Saved Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Saved Successfully'), 'data' => $category, 'table' => '#categories_table']);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id) {
        $category = Category::find($id);
        if (!$request->ajax()) {
            return back();
        } else {
            return view('backend.user.category.modal.view', compact('category', 'id'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id) {
        $category = Category::find($id);
        if (!$request->ajax()) {
            return back();
        } else {
            return view('backend.user.category.modal.edit', compact('category', 'id'));
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
            'name'     => 'required',
            'status'   => 'required',
            'position' => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('categories.edit', $id)
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $category              = Category::find($id);
        $category->name        = $request->input('name');
        $category->description = $request->input('description');
        $category->status      = $request->input('status');
        $category->position    = $request->input('position');

        $category->save();
        $category->status = status($category->status);

        if (!$request->ajax()) {
            return redirect()->route('categories.index')->with('success', _lang('Updated Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Updated Successfully'), 'data' => $category, 'table' => '#categories_table']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $category = Category::find($id);
        $category->delete();
        return redirect()->route('categories.index')->with('success', _lang('Deleted Successfully'));
    }
}