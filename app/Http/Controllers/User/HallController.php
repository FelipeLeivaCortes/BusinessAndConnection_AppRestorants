<?php

namespace App\Http\Controllers\User;

use Validator;
use App\Models\Hall;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class HallController extends Controller {

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
        $halls  = Hall::all()->sortByDesc("id");
        return view('backend.user.hall.list', compact('halls', 'assets'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        if (!$request->ajax()) {
            return back();
        } else {
            return view('backend.user.hall.modal.create');
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
            'capacity' => 'required|integer',
            'status'   => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('halls.create')
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $hall              = new Hall();
        $hall->name        = $request->input('name');
        $hall->capacity    = $request->input('capacity');
        $hall->description = $request->input('description');
        $hall->status      = $request->input('status');

        $hall->save();
        $hall->status = status($hall->status);

        if (!$request->ajax()) {
            return redirect()->route('halls.create')->with('success', _lang('Saved Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Saved Successfully'), 'data' => $hall, 'table' => '#halls_table']);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id) {
        $alert_col = 'col-lg-10 offset-lg-1';
        $hall      = Hall::with('tables')->find($id);
        return view('backend.user.hall.table-setup', compact('hall', 'id', 'alert_col'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id) {
        $hall = Hall::find($id);
        if (!$request->ajax()) {
            return back();
        } else {
            return view('backend.user.hall.modal.edit', compact('hall', 'id'));
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
            'capacity' => 'required|integer',
            'status'   => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('halls.edit', $id)
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $hall              = Hall::find($id);
        $hall->name        = $request->input('name');
        $hall->capacity    = $request->input('capacity');
        $hall->description = $request->input('description');
        $hall->status      = $request->input('status');

        $hall->save();
        $hall->status = status($hall->status);

        if (!$request->ajax()) {
            return redirect()->route('halls.index')->with('success', _lang('Updated Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Updated Successfully'), 'data' => $hall, 'table' => '#halls_table']);
        }

    }

    public function update_setup(Request $request, $id) {
        DB::beginTransaction();

        foreach(json_decode($request->tables) as $data){
            $table = Table::find($data->id);
            $table->css = $data->css;
            $table->save();
        }

        $hall = Hall::find($id);
        $hall->css = $request->hallView;
        $hall->save();

        DB::commit();

        if (!$request->ajax()) {
            return back()->with('success', _lang('Updated Successfully'));
        } else {
            return response()->json(['result' => 'success', 'message' => _lang('Updated Successfully')]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $hall = Hall::find($id);
        $hall->delete();
        return redirect()->route('halls.index')->with('success', _lang('Deleted Successfully'));
    }
}