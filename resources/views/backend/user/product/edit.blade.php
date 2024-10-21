@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-10 offset-lg-1">
		<div class="card">
			<div class="card-header">
				<span class="panel-title">{{ _lang('Update Item') }}</span>
			</div>
			<div class="card-body">
				<form method="post" id="update_product" class="validate" autocomplete="off" action="{{ route('products.update', $id) }}" enctype="multipart/form-data">
					@csrf
					<input name="_method" type="hidden" value="PATCH">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Name') }}</label>						
								<input type="text" class="form-control" name="name" value="{{ $product->name }}" required>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Category') }}</label>						
								<select class="form-control auto-select select2-ajax" data-selected="{{ $product->category_id }}" data-table="categories" 
								data-value="id" data-display="name" data-where="3" data-href="{{ route('categories.create') }}" data-title="{{ _lang('New Item Category') }}" name="category_id" required>	
									<option value="">{{ _lang('Select One') }}</option>
									@foreach(\App\Models\CategoryModel::active()->get() as $category)
									<option value="{{ $category->id }}">{{ $category->name }}</option>
									@endforeach
								</select>
							</div>
						</div>

						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Image') }} (400px X 400px)</label>						
								<input type="file" class="form-control dropify" name="image" data-default-file="{{ asset('public/uploads/media/' . $product->image) }}">
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Price') }} ({{ currency_symbol(request()->activeBusiness->currency) }})</label>						
								<input type="text" class="form-control" name="price" value="{{ $product->price }}" required>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Special Price') }} ({{ currency_symbol(request()->activeBusiness->currency) }})</label>						
								<input type="text" class="form-control" name="special_price" value="{{ $product->special_price }}">
							</div>
						</div>

						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Description') }}</label>						
								<textarea class="form-control" name="description">{{ $product->description }}</textarea>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Product Type') }}</label>						
								<select class="form-control auto-select" id="product_type" data-selected="{{ $product->product_type }}" name="product_type">
									<option value="1">{{ _lang('General Product') }}</option>
									<option value="2">{{ _lang('Variation Product') }}</option>
								</select>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Status') }}</label>						
								<select class="form-control auto-select" data-selected="{{ $product->status }}" name="status" required>
									<option value="1">{{ _lang('Active') }}</option>
									<option value="0">{{ _lang('Disabled') }}</option>
								</select>
							</div>
						</div>

						@if($product->product_type == '2')
							<div class="col-md-12">
								<div class="form-group">
									<div class="form-check">
										<label class="form-check-label text-danger">
											<input type="checkbox" class="form-check-input" value="1" name="update_variation" id="update_variation"> {{ _lang('Update Variations?') }}
										</label>
									</div>
								</div>
							</div>
						@endif


						<div class="col-md-12 variable-product {{ $product->product_type == '2' ? '' : 'd-none' }}">

							@if(! $product->product_options->isEmpty())
								@foreach($product->product_options as $product_option)
								<div class="row product-option" {{ $loop->first ? 'id=option' : '' }}>
									<div class="col-md-6">
										<div class="form-group">
											<label class="control-label">{{ _lang('Option Name') }}</label>
											<input type="text" class="form-control product_option" name="product_option[]" value="{{ $product_option->name }}" placeholder="Ex - Color">
										</div>
									</div>

									<div class="col-md-6">
										<div class="form-group">
											<label class="control-label">{{ _lang('Option Value') }}</label>
											<input type="text" class="form-control product_option_value" name="product_option_value[]" value="{{ str_replace(array('[', '"',']'),'',$product_option->items->pluck('name')) }}" placeholder="Ex - Red, Green, Blue">
										</div>
									</div>
								</div>
								@endforeach
							@else
								<div class="row product-option" id="option">
									<div class="col-md-6">
										<div class="form-group">
											<label class="control-label">{{ _lang('Option Name') }}</label>
											<input type="text" class="form-control product_option" name="product_option[]" value="" placeholder="Ex - Color">
										</div>
									</div>

									<div class="col-md-6">
										<div class="form-group">
											<label class="control-label">{{ _lang('Option Value') }}</label>
											<input type="text" class="form-control product_option_value" name="product_option_value[]" placeholder="Ex - Red, Green, Blue">
										</div>
									</div>
								</div>
							@endif

							<button type="button" class="btn btn-dark btn-xs float-right" id="add_more_option">
								<i class="ti-plus"></i> {{ _lang('Add More Option') }}
							</button>
						</div>

						<!--Product Variations-->
						<div class="col-md-12 variable-product {{ $product->product_type == '2' ? '' : 'd-none' }}">
							<div class="card">

								<div class="card-header">
									<span class="panel-title">{{ _lang('Variations Prices') }}</span>
								</div>

								<div class="card-body">
									<div class="table-responsive">
										<table class="table table-bordered" id="variations-prices-table">
											<thead>
												@foreach($product->product_options as $product_option)
													<th>{{ $product_option->name }}</th>
												@endforeach
												<th>{{ _lang('Price') }}</th>
												<th>{{ _lang('Special Price') }}</th>
												<th class="text-center">{{ _lang('Is Available') }}</th>
											</thead>
											<tbody>
												@foreach($product->variation_prices as $variation_price)
													<tr>
														@foreach(json_decode($variation_price->option) as $option)
															<td>{{ $option->name }}</td>
														@endforeach
														<td>
															<input type="text" name="variation_price[]" class="form-control" value="{{ $variation_price->price }}" placeholder="{{ _lang('Regular Price') }}">
														</td>
														<td>
															<input type="text" name="variation_special_price[]" class="form-control" value="{{ $variation_price->special_price }}" placeholder="{{ _lang('Special Price') }}">
														</td>
														<td class="text-center">
															<input type="hidden" name="is_available[{{ $loop->index }}]" value="0">
															<input type="checkbox" name="is_available[{{ $loop->index }}]" value="1" {{ $variation_price->is_available == 1 ? 'checked' : '' }}>
														</td>
													</tr>
												@endforeach
											</tbody>
										</table>
									</div>

									<button type="button" class="btn btn-light btn-xs btn-block" id="generate_variations">
										<i class="ti-reload"></i> {{ _lang('Generate Variations') }}
									</button>

									<div class="text-center">
										<small>{{ _lang('Click Generate Variations after adding/updating all Option Value') }}</small>
									</div>
								</div>

							</div>
						</div><!--End Product Variations-->
				
						<div class="col-md-12 mt-2">
							<div class="form-group">
								<button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i> {{ _lang('Update Product') }}</button>
							</div>
						</div>
					</div>
			    </form>
			</div>
		</div>
    </div>
</div>
@endsection

@section('js-script')
<script src="{{ asset('public/backend/assets/js/product.js') }}"></script>
@endsection
					