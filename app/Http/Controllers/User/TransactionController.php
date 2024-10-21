<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use DataTables;
use Illuminate\Http\Request;
use Validator;

class TransactionController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $assets = ['datatable'];
        return view('backend.user.transaction.list', compact('assets'));
    }

    public function get_table_data() {
        $transactions = Transaction::select('transactions.*')
            ->with('transaction_category')
            ->orderBy("transactions.id", "desc");

        return Datatables::eloquent($transactions)
            ->editColumn('amount', function ($transaction) {
                return formatAmount($transaction->amount, currency_symbol(request()->activeBusiness->currency));
            })
            ->addColumn('action', function ($transaction) {
                return '<div class="dropdown text-center">'
                . '<button class="btn btn-outline-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">' . _lang('Action')
                . '</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item ajax-modal" href="' . route('transactions.edit', $transaction['id']) . '" data-title="' . _lang('Update Expense') . '"><i class="fas fa-pencil-alt"></i> ' . _lang('Edit') . '</a>'
                . '<a class="dropdown-item ajax-modal" href="' . route('transactions.show', $transaction['id']) . '" data-title="' . _lang('Expense Details') . '"><i class="fas fa-eye"></i> ' . _lang('Details') . '</a>'
                . '<form action="' . route('transactions.destroy', $transaction['id']) . '" method="post">'
                . csrf_field()
                . '<input name="_method" type="hidden" value="DELETE">'
                . '<button class="dropdown-item btn-remove" type="submit"><i class="fas fa-trash-alt"></i> ' . _lang('Delete') . '</button>'
                    . '</form>'
                    . '</div>'
                    . '</div>';
            })
            ->setRowId(function ($transaction) {
                return "row_" . $transaction->id;
            })
            ->rawColumns(['action'])
            ->make(true);
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
            return view('backend.user.transaction.modal.create');
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
            'trans_date'              => 'required',
            'transaction_category_id' => 'required',
            'amount'                  => 'required',
            'method'                  => 'required',
            'attachment'              => 'nullable|mimes:jpeg,JPEG,png,PNG,jpg,doc,pdf,docx,zip',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('transactions.create')
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $attachment = '';
        if ($request->hasfile('attachment')) {
            $file       = $request->file('attachment');
            $attachment = time() . $file->getClientOriginalName();
            $file->move(public_path() . "/uploads/media/", $attachment);
        }

        $transaction                          = new Transaction();
        $transaction->trans_date              = $request->input('trans_date');
        $transaction->transaction_category_id = $request->input('transaction_category_id');
        $transaction->dr_cr                   = 'dr';
        $transaction->type                    = 'expense';
        $transaction->amount                  = $request->input('amount');
        $transaction->method                  = $request->input('method');
        $transaction->reference               = $request->input('reference');
        $transaction->description             = $request->input('description');
        $transaction->employee_id             = $request->input('employee_id');
        $transaction->attachment              = $attachment;

        $transaction->save();

        if (!$request->ajax()) {
            return redirect()->route('transactions.create')->with('success', _lang('Saved Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Saved Successfully'), 'data' => $transaction, 'table' => '#transactions_table']);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id) {
        $transaction = Transaction::find($id);
        if (!$request->ajax()) {
            return back();
        } else {
            return view('backend.user.transaction.modal.view', compact('transaction', 'id'));
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id) {
        $transaction = Transaction::find($id);
        if (!$request->ajax()) {
            return back();
        } else {
            return view('backend.user.transaction.modal.edit', compact('transaction', 'id'));
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
            'trans_date'              => 'required',
            'transaction_category_id' => 'required',
            'amount'                  => 'required',
            'method'                  => 'required',
            'attachment'              => 'nullable|mimes:jpeg,JPEG,png,PNG,jpg,doc,pdf,docx,zip',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('transactions.edit', $id)
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        if ($request->hasfile('attachment')) {
            $file       = $request->file('attachment');
            $attachment = time() . $file->getClientOriginalName();
            $file->move(public_path() . "/uploads/media/", $attachment);
        }

        $transaction                          = Transaction::find($id);
        $transaction->trans_date              = $request->input('trans_date');
        $transaction->transaction_category_id = $request->input('transaction_category_id');
        $transaction->dr_cr                   = 'dr';
        $transaction->type                    = 'expense';
        $transaction->amount                  = $request->input('amount');
        $transaction->method                  = $request->input('method');
        $transaction->reference               = $request->input('reference');
        $transaction->description             = $request->input('description');
        $transaction->employee_id             = $request->input('employee_id');
        if ($request->hasfile('attachment')) {
            $transaction->attachment = $attachment;
        }

        $transaction->save();

        if (!$request->ajax()) {
            return redirect()->route('transactions.index')->with('success', _lang('Updated Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Updated Successfully'), 'data' => $transaction, 'table' => '#transactions_table']);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $transaction = Transaction::find($id);
        $transaction->delete();
        return redirect()->route('transactions.index')->with('success', _lang('Deleted Successfully'));
    }
}