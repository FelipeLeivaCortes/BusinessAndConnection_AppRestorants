<div class="container">
    <div class="row">
        <div class="col-lg-12 my-3 d-flex justify-content-between">
            <div></div>
            <h4 class="bg-white d-inline-block py-2 px-4 rounded text-primary">{{ request()->activeBusiness->name }}</h4>
            <div></div>
        </div>

        <div class="col-lg-12">
            <div class="card overflow-auto">
                <div class="card-header d-sm-flex align-items-center justify-content-between">
                    <span class="panel-title">{{ _lang('Active Orders') }}</span>
                    <span class="dropdown">
                        <button class="btn btn-primary dropdown-toggle btn-xs  mr-1" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-plus"></i> {{ _lang('New Order') }}
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                            <a href="{{ route('pos.table') }}" class="dropdown-item ajax-link select-table"><i class="fas fa-hotel mr-2"></i>{{ _lang('Select Table') }}</a>
                            @if(get_business_option('order_delivery_status', 0, business_id()) == 1)
                            <a href="{{ route('pos.sell', ['d'.rand().time(), 'delivery']) }}" class="dropdown-item ajax-link"><i class="fas fa-biking mr-2"></i>{{ _lang('New Delivery') }}</a>
                            @endif

                            @if(get_business_option('order_takeway_status', 0, business_id()) == 1)
                            <a href="{{ route('pos.sell', ['t'.rand().time(), 'takeway']) }}" class="dropdown-item ajax-link"><i class="fas fa-people-carry mr-2"></i>{{ _lang('New Takeway') }}</a>
                            @endif
                        </div>
                    </span>
                </div>
                <div class="card-body">
                    <table class="table data-table">
                        <thead>
                            <tr>
                                <th>{{ _lang('Order Number') }}</th>
                                <th>{{ _lang('Sub Total') }}</th>
                                <th>{{ _lang('Grand Total') }}</th>
                                <th class="text-center">{{ _lang('Status') }}</th>
                                <th>{{ _lang('Order Type') }}</th>
                                <th class="text-center">{{ _lang('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allCart as $tableId => $cartItem)
                            @php $cart = new \App\Utilities\Cart($tableId); @endphp
                            <tr>
                                <td>{{ $cart->getOrderId() == null ? _lang('N/A') : $cart->getOrderNumber() }}</td>
                                <td>{{ $cart->getSubTotal(true) }}</td>
                                <td>{{ $cart->getGrandTotal(true) }}</td>
                                <td class="text-center">{!! xss_clean($cart->getOrderStatus(true)) !!}</td>
                                <td>{{ ucwords($cart->getOrderType()) }}</td>
                                <td class="text-center">
                                    <button class="btn btn-primary btn-xs ajax-link" data-link="{{ route('pos.sell', [$tableId, $cart->getOrderType()]) }}">{{ _lang('View Cart') }}</button>
                                    <a class="btn btn-danger btn-xs btn-remove" href="{{ route('pos.empty_cart', $tableId) }}">{{ _lang('Remove') }}</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>