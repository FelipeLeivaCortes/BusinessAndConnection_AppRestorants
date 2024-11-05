<?php

namespace App\Http\Controllers\User;

use Validator;
use App\Models\Hall;
use App\Models\Table;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TableController extends Controller {

     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    public function index(Request $request, $hallId) {
        $assets = ['datatable'];
        $hall = Hall::find($hallId);
        $tables = Table::where('hall_id', $hallId)->get();
        return view('backend.user.table.list', compact('hallId', 'tables', 'assets', 'hall'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $hallId) {
        if (!$request->ajax()) {
            return back();
        } else {
            return view('backend.user.table.modal.create', compact('hallId'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $hallId) {
        $validator = Validator::make($request->all(), [
            'table_no'    => 'required|integer',
            'type'        => 'required',
            'chair_limit' => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return back()->withErrors($validator)->withInput();
            }
        }

        $table              = new Table();
        $table->table_no    = $request->input('table_no');
        $table->hall_id     = $hallId;
        $table->type        = $request->input('type');
        $table->chair_limit = $request->input('chair_limit');

        $table->save();

        if (!$request->ajax()) {
            return back()->with('success', _lang('Saved Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Saved Successfully'), 'data' => $table, 'table' => '#tables_table']);
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id) {
        $table = Table::find($id);
        if (!$request->ajax()) {
            return back();
        } else {
            return view('backend.user.table.modal.edit', compact('table', 'id'));
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
            'table_no'    => 'required|integer',
            'type'        => 'required',
            'chair_limit' => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return back()->withErrors($validator)->withInput();
            }
        }

        $table              = Table::find($id);
        $table->table_no    = $request->input('table_no');
        $table->type        = $request->input('type');
        $table->chair_limit = $request->input('chair_limit');

        $table->save();

        if (!$request->ajax()) {
            return back()->with('success', _lang('Updated Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Updated Successfully'), 'data' => $table, 'table' => '#tables_table']);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $table = Table::find($id);
        $table->delete();
        return back()->with('success', _lang('Deleted Successfully'));
    }

    /**
     * Show the form for update the background image.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit_background(Request $request, $hallId) {
        if (!$request->ajax()) {
            return back();
        } else {
            return view('backend.user.table.modal.update_background', compact('hallId'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update_background(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'new_url'        => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return back()->withErrors($validator)->withInput();
            }
        }

        $hall   = Hall::find($id);

        if (!$hall) {
            return back()->with('error', 'Hall not found.');
        }

        if ($request->hasFile('new_url')) {
            $file               = $request->file('new_url');
            $filename           = time() . '.' . $file->getClientOriginalExtension();
            $destinationPath    = public_path('uploads/media');

            $file->move($destinationPath, $filename);

            $imageUrl           = asset('public/uploads/media/' . $filename);
            $hall->css          = "background-image: url('$imageUrl') !important;";
            $hall->save();
        }
    
        return back()->with('success', _lang('Updated Successfully'));
    }
}