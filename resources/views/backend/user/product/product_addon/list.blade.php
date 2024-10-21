@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-12">
		<div class="card">
		    <div class="card-header d-sm-flex align-items-center justify-content-between">
				<span class="panel-title">{{ $product->name }} - {{ _lang('Addon Items') }}</span>

                <div>
                    <a href="{{ route('products.index') }}" class="btn btn-dark btn-xs"><i class="fas fa-list-ul"></i> {{ _lang('Product List') }}</a>
				    <a class="btn btn-primary btn-xs ajax-modal" data-title="{{ _lang('Add Addons Item') }}" href="{{ route('product_addons.create', $productId) }}"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
			    </div>
			</div>
			<div class="card-body">
				<table id="product_addons_table" class="table data-table">
					<thead>
					    <tr>
                            <th>{{ _lang('Name') }}</th>
                            <th>{{ _lang('Price') }}</th>
                            <th>{{ _lang('Description') }}</th>
                            <th class="text-center">{{ _lang('Action') }}</th>
					    </tr>
					</thead>
					<tbody>
					    @foreach($productaddons as $productaddon)
					    <tr data-id="row_{{ $productaddon->id }}">
							<td class='name'>{{ $productaddon->name }}</td>
							<td class='price'>{{ formatAmount($productaddon->price, currency_symbol(request()->activeBusiness->currency)) }}</td>
							<td class='description'>{{ $productaddon->description }}</td>
							
							<td class="text-center">
								<span class="dropdown">
								  <button class="btn btn-outline-primary dropdown-toggle btn-xs" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								  {{ _lang('Action') }}
								  </button>
								  <form action="{{ route('product_addons.destroy', $productaddon['id']) }}" method="post">
									{{ csrf_field() }}
									<input name="_method" type="hidden" value="DELETE">

									<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
										<a href="{{ route('product_addons.edit', $productaddon['id']) }}" data-title="{{ _lang('Update Addons Item') }}" class="dropdown-item dropdown-edit ajax-modal"><i class="fas fa-pencil-alt"></i> {{ _lang('Edit') }}</a>
										<button class="btn-remove dropdown-item" type="submit"><i class="fas fa-trash-alt"></i> {{ _lang('Delete') }}</button>
									</div>
								  </form>
								</span>
							</td>
					    </tr>
					    @endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
@endsection