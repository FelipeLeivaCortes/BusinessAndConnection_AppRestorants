<div class="card">
    <div class="card-header">
        <span class="panel-title">{{ _lang('Orders') }}</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table border-bottom">
                <thead>
                    <tr>
                        <th>{{ _lang('Order Number') }}</th>
                        <th class="text-right">{{ _lang('Sub Total') }}</th>
                        <th class="text-right">{{ _lang('Grand Total') }}</th>
                        <th class="text-right">{{ _lang('Paid') }}</th>
                        <th class="text-center">{{ _lang('Status') }}</th>
                        <th>{{ _lang('Order Type') }}</th>
                        <th class="text-center">{{ _lang('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td>{{ $order->order_number }}</td>
                            <td class="text-right">{{ formatAmount($order->sub_total, currency_symbol(request()->activeBusiness->currency)) }}</td>
                            <td class="text-right">{{ formatAmount($order->grand_total, currency_symbol(request()->activeBusiness->currency)) }}</td>
                            <td class="text-right">{{ formatAmount($order->paid, currency_symbol(request()->activeBusiness->currency)) }}</td>
                            <td class="text-center">{!! xss_clean(order_status($order->status)) !!}</td>
                            <td>{{ ucwords($order->order_type) }}</td>
                            <td class="text-center">
                                <div class="dropdown text-center">
                                    <button class="btn btn-outline-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">{{ _lang('Action') }}</button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" target="_blank" href="{{ route('pos.print_customer_receipt', $order['id']) }}"><i class="fas fa-print mr-1"></i> {{ _lang('POS Receipt') }}</a>
                                        <a class="dropdown-item" href="{{ route('orders.show', $order['id']) }}"><i class="fas fa-info-circle mr-1"></i> {{ _lang('Order Details') }}</a>
                                        <form action="{{ route('orders.destroy', $order['id']) }}" method="post">
                                            @csrf
                                            <input name="_method" type="hidden" value="DELETE">
                                            <button class="dropdown-item btn-remove" type="submit"><i class="far fa-trash-alt mr-1"></i>{{ _lang('Delete') }}</button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="float-right">
            {{ $orders->links() }}
        </div>
    </div>
</div>