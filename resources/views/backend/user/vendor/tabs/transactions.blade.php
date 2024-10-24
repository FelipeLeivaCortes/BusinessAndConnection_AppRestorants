<div class="card">
    <div class="card-header">
        <span class="panel-title">{{ _lang('Transactions') }}</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table border-bottom">
                <thead>
                    <tr>
                        <th>{{ _lang('Date') }}</th>
                        <th>{{ _lang('Category') }}</th>
                        <th>{{ _lang('Description') }}</th>
                        <th class="text-right">{{ _lang('Amount') }}</th>
                        <th class="text-center">{{ _lang('View') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $transaction)
                        <tr>
                            @php
                            $symbol = $transaction->dr_cr == 'dr' ? '-' : '+';
                            $class  = $transaction->dr_cr == 'dr' ? 'text-danger' : 'text-success';
                            @endphp
                            
                            <td>{{ $transaction->trans_date }}</td>
                            <td>
                                {{ $transaction->transaction_category->name }}
                                @if($transaction->ref_id != null && $transaction->ref_type == 'purchase')
                                <br><a href="{{ route('purchases.show', $transaction->ref_id) }}" target="_blank"><i class="far fa-eye mr-1"></i>{{ _lang('See Purchase') }}</a>
                                @endif
                            </td>				
                            <td>{{ $transaction->description }}</td>				
							<td class="text-right"><span class="{{ $class }}">{{ $symbol.' '.formatAmount($transaction->amount, currency_symbol(request()->activeBusiness->currency)) }}</span></td>
                            <td class="text-center">
                                <a class="btn btn-xs btn-outline-primary ajax-modal" data-title="{{ _lang('Transaction Details') }}" href="{{ route('transactions.show', $transaction['id']) }}"><i class="far fa-eye mr-1"></i>{{ _lang('Preview') }}</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="float-right">
            {{ $transactions->links() }}
        </div>

    </div>
</div>