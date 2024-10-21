<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class BusinessController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware(function ($request, $next) {

            $route_name = request()->route()->getName();
            if ($route_name == 'business.store') {
                if (has_limit('business', 'business_limit') <= 0) {
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
        $assets    = ['datatable'];
        $businesss = Business::with('role')->get()->sortByDesc("id");
        return view('backend.user.business.list', compact('businesss', 'assets'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        $alert_col = 'col-lg-8 offset-lg-2';
        return view('backend.user.business.create', compact('alert_col'));
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
            'country'  => 'required',
            'currency' => 'required',
            'logo'     => 'nullable|image|max:2048',
            'status'   => 'required',
            'default'  => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('business.create')
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $logo = 'default/default-company-logo.png';
        if ($request->hasfile('logo')) {
            $file = $request->file('logo');
            $logo = time() . $file->getClientOriginalName();
            $file->move(public_path() . "/uploads/media/", $logo);
        }

        DB::beginTransaction();

        $business           = new Business();
        $business->name     = $request->input('name');
        $business->reg_no   = $request->input('reg_no');
        $business->vat_id   = $request->input('vat_id');
        $business->user_id  = auth()->id();
        $business->email    = $request->input('email');
        $business->phone    = $request->input('phone');
        $business->country  = $request->input('country');
        $business->currency = $request->input('currency');
        $business->address  = $request->input('address');
        $business->logo     = $logo;
        $business->status   = $request->input('status');
        if ($request->default == 1) {
            Business::where('default', 1)->update(['default' => 0]);
            $business->default = $request->input('default');
        }

        $business->save();

        $business->users()->attach($business->user_id, ['owner_id' => $business->user_id, 'is_active' => count($request->businessList) == 0 ? 1 : 0]);

        DB::commit();

        if ($business->id > 0) {
            return redirect()->route('business.index')->with('success', _lang('Saved Successfully'));
        }
    }

    /**
     * Display System User list
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function users(Request $request, $id) {
        $assets   = ['datatable'];
        $business = Business::with('users')->find($id);
        return view('backend.user.business.system_users', compact('business', 'id', 'assets'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id) {
        $alert_col = 'col-lg-8 offset-lg-2';
        $business  = Business::owner()->find($id);

        return view('backend.user.business.edit', compact('business', 'id', 'alert_col'));
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
            'country'  => 'required',
            'currency' => 'required',
            'logo'     => 'nullable|image|max:2048',
            'status'   => 'required',
            'default'  => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('business.edit', $id)
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        if ($request->hasfile('logo')) {
            $file = $request->file('logo');
            $logo = time() . $file->getClientOriginalName();
            $file->move(public_path() . "/uploads/media/", $logo);
        }

        DB::beginTransaction();

        $business = Business::owner()->find($id);

        $business->name     = $request->input('name');
        $business->reg_no   = $request->input('reg_no');
        $business->vat_id   = $request->input('vat_id');
        $business->user_id  = auth()->id();
        $business->email    = $request->input('email');
        $business->phone    = $request->input('phone');
        $business->country  = $request->input('country');
        $business->currency = $request->input('currency');
        $business->address  = $request->input('address');
        if ($request->hasfile('logo')) {
            $business->logo = $logo;
        }
        $business->status = $request->input('status');

        if ($request->default == 1) {
            Business::where('default', 1)->update(['default' => 0]);
            $business->default = $request->input('default');
        }

        $business->save();

        DB::commit();

        if (!$request->ajax()) {
            return redirect()->route('business.index')->with('success', _lang('Updated Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Updated Successfully'), 'data' => $business, 'table' => '#business_table']);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $business = Business::owner()->find($id);
        if ($business->default == 1) {
            return redirect()->route('business.index')->with('error', _lang('Sorry, You will not be able to delete default business!'));
        }
        $business->delete();
        return redirect()->route('business.index')->with('success', _lang('Deleted Successfully'));
    }

    /** Switch Business Account **/
    public function switch_business(Request $request, $id) {
        $user = auth()->user();
        if ($user->user_type != 'user') {
            return back()->with('error', _lang('Permission denied !'));
        }
        $business = $user->business()->where('business.id', $id)->first();

        if (!$business) {
            return back()->with('error', _lang('Permission denied !'));
        }

        $user->business()->updateExistingPivot($request->activeBusiness->id, ['is_active' => 0]);

        $user->business()->updateExistingPivot($id, ['is_active' => 1]);

        return redirect()->route('dashboard.index')->with('success', _lang('Business switched to') . ' ' . $request->activeBusiness->name);
    }

}