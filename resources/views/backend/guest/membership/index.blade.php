@extends('layouts.app')

@section('content')
<div id="pricing-table">
    <div class="row justify-content-center">      

        @if($package != null)
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header text-center">
                    <span class="panel-title">{{ _lang('Membership Details') }}</span>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <tr>
                            <td>{{ _lang('Membership Type') }}</td>
                            <td>{{ ucwords(auth()->user()->membership_type) }}</td>
                        </tr>
                        <tr><td>{{ _lang('Pasckage Name') }}</td><td>{{ $package->name }}</td></tr>
                        <tr>
                            <td>{{ _lang('Pasckage Cost') }}</td>
                            <td>{{ decimalPlace($package->cost, currency_symbol()) }} / {{ ucwords($package->package_type) }}</td>
                        </tr>
                        <tr><td>{{ _lang('Restaurant Limit') }}</td><td>{{ $package->business_limit != '-1' ? $package->business_limit : _lang('Unlimited') }}</td></tr>
                        <tr><td>{{ _lang('System User Limit') }}</td><td>{{ $package->staff_limit != '-1' ? $package->staff_limit : _lang('Unlimited') }}</td></tr>
                        <tr><td>{{ _lang('Item Limit') }}</td><td>{{ $package->item_limit != '-1' ? $package->item_limit : _lang('Unlimited') }}</td></tr>
                        <tr><td>{{ _lang('Order Limit') }} ({{ ucwords($package->package_type) }})</td><td>{{ $package->order_limit != '-1' ? $package->order_limit : _lang('Unlimited') }}</td></tr>
                        <tr>
                            <td>{{ _lang('Payroll Module') }}</td>
                            <td>
                                @if($package->payroll_module == 1)
                                {!! xss_clean(show_status(_lang('Yes'), 'success')) !!}
                                @else
                                {!! xss_clean(show_status(_lang('No'), 'danger')) !!}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>{{ _lang('Subscription Date') }}</td>
                            <td>{{ auth()->user()->subscription_date }}</td>
                        </tr>
                        <tr>
                            <td>{{ _lang('Subscription Expired') }}</td>
                            <td>{{ auth()->user()->valid_to }}</td>
                        </tr>
                        <tr>
                            <td>{{ _lang('Last Payment') }}</td>
                            <td>{{ $lastPayment ? decimalPlace($lastPayment->amount, currency_symbol()) : _lang('N/A') }}</td>
                        </tr>
                        <tr>
                            <td>{{ _lang('Last Payment Date') }}</td>
                            <td>{{ $lastPayment ? $lastPayment->created_at : _lang('N/A') }}</td>
                        </tr>
                    </table>
                    <form action="{{ route('membership.choose_package') }}" method="post">
                        @csrf
                        <input type="hidden" name="package_id" value="{{ $package->id }}">
                        <a href="{{ route('membership.payment_gateways') }}" class="btn btn-primary btn-block mt-4">{{ _lang('Renew Membership') }}</a>
                        <a href="{{ route('membership.packages') }}" class="btn btn-danger btn-block mt-2" id="change-package">{{ _lang('Change Package') }}</a>
                    </form>
                </div>
            </div>
        </div>

        @endif
    </div>
</div>
@endsection

@section('js-script')
<script>
(function ($) {
    "use strict";

    $(document).on('click','#change-package', function(e){
        e.preventDefault();
        var link = $(this).attr('href');

        Swal.fire({
			text: '{{ _lang('Once you process then you will not able to rollback current subscription. You need to repay for new selected package !') }}',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: '{{ _lang('Yes Process') }}',
			cancelButtonText: $lang_cancel_button_text
		}).then((result) => {
			if (result.value) {
				window.location.href = link;
			}
		});
    });
    
})(jQuery);
</script>
@endsection