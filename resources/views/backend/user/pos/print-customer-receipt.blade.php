<!DOCTYPE html>
<html lang="en">
    <head>
        <title>{{ $title }}</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <style type="text/css">
            @page { margin: 10px 5px; }
            /*body{font-size: 12px; margin: 10px; font-family: 'DejaVu Sans', serif;}*/
            body{font-size: 12px; margin: 10px; font-family: 'DejaVu Sans', serif; width: 2.8in; margin: auto;}
            table{width: 100%; padding: 0px;}
            tabel > thead > th{ text-align: left;}
            .header-details{text-align: center;}
            p{margin-bottom: -8px; font-size: 11px;}
            .header-details p{margin-bottom: -8px;}
            .header-details h3{margin-bottom: -5px;}
            .divider{border-bottom: 1px dashed #393b39; margin-bottom: 5px; margin-top: 5px;}
            .mt-2{ margin-top: 15px;}
            .mt-3{ margin-top: 20px;}
            .mt-4{ margin-top: 30px;}
            .text-center{text-align: center;}
            .text-right{text-align: right;}
            .text-nowrap{white-space: nowrap !important;}
            .border-top{ border-top: 1px dashed #393b39;}
            .border-top-bottom{ border-top: 1px dashed #393b39; border-bottom: 1px dashed #393b39;}
            .pt-2{padding-top: 5px;}
		</style>
    </head>
	<body>  
        <div class="header-details">
            <h3>{{ request()->activeBusiness->name }}</h3>
            <p>{{ request()->activeBusiness->address }}</p>
            <p>{{ _lang('Phone') }}: {{ request()->activeBusiness->phone }}</p> 
            @if(request()->activeBusiness->vat_id != '')
            <p>{{ _lang('VAT ID') }}: {{ request()->activeBusiness->vat_id }}</p> 
            @endif

            <p class="mt-2">{{ _lang('Order No') }}: {{ $order->order_number }}</p>
            <p>{{ _lang('Sale Time') }}: {{ $order->updated_at }}</p>
        </div>

		<table class="mt-3">
           <tr>
                <td class="border-top-bottom"><b>{{ _lang('Product') }}</b></td>
                <td class="border-top-bottom text-center"><b>{{ _lang('Qty') }}</b></td>
                <td class="border-top-bottom text-right"><b>{{ _lang('Price') }}</b></td>
                <td class="border-top-bottom text-right"><b>{{ _lang('Total') }}</b></td>
           </tr>
           <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right text-nowrap">{{ formatAmount($item->unit_cost) }}</td>
                    <td class="text-right text-nowrap">{{ formatAmount($item->sub_total) }}</td>
                </tr>
                @endforeach
                <tr>
                    <td class="border-top pt-2" colspan="3"><b>{{ _lang('Sub Total') }}:</b></td>
                    <td class="text-right border-top pt-2 text-nowrap"><b>{{ formatAmount($order->sub_total) }}</b></td>
                </tr>

                @foreach($order->taxes as $tax)
                <tr>
                    <td colspan="3">{{ $tax->name }} ({{ $tax->rate }}%):</td>
                    <td class="text-right text-nowrap">+ {{ formatAmount($tax->amount) }}</td>
                </tr>
                @endforeach

                @if($order->service_charge > 0)
                <tr>
                    <td colspan="3">{{ _lang('Service Charge') }}:</td>
                    <td class="text-right text-nowrap">+ {{ formatAmount($order->service_charge) }}</td>
                </tr>
                @endif

                @if($order->discount > 0)
                <tr>
                    <td colspan="3">{{ _lang('Discount') }} ({{ $order->discount_percentage }}%):</td>
                    <td class="text-right text-nowrap">- {{ formatAmount($order->discount) }}</td>
                </div>
                @endif

                <tr>
                    <td class="border-top pt-2" colspan="3"><b>{{ _lang('Grand Total') }}:</b></td>
                    <td class="text-right border-top pt-2 text-nowrap"><b>{{ formatAmount($order->grand_total) }}</b></td>
                </tr>

                <tr>
                    <td colspan="3"><b>{{ _lang('Paid') }}:</b></td>
                    <td class="text-right text-nowrap"><b>{{ formatAmount($order->paid) }}</b></td>
                </tr>

                @if($order->paid > $order->grand_total)
                <tr>
                    <td colspan="3">{{ _lang('Change') }}:</td>
                    <td class="text-right text-nowrap">{{ formatAmount($order->paid - $order->grand_total) }}</td>
                </tr>
                @endif

                @if($order->paid > 0)
                <tr>
                    <td colspan="3">{{ _lang('Pay Mode') }}:</td>
                    <td class="text-right text-nowrap">{{ str_replace('_', ' ', $order->payment_method) }}</td>
                </tr>
                @endif
           </tbody>
		</table>

        <div class="divider"></div>

        <div class="text-center">
            {!! xss_clean(get_business_option('receipt_footer', 'Thank You', business_id())) !!}
        </div>
    </body>

    <script type="text/javascript">
        window.print();
        setTimeout(function () {
            window.close();
        }, 500);
    </script>
</html>