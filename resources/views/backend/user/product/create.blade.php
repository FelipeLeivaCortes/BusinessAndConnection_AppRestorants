@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-10 offset-lg-1">
		<div class="card">
			<div class="card-header">
				<span class="panel-title">{{ _lang('Add New Item') }}</span>
			</div>
			<div class="card-body">
			    <form method="post" class="validate" autocomplete="off" action="{{ route('products.store') }}" enctype="multipart/form-data">
					{{ csrf_field() }}
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Name') }}</label>						
								<input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Category') }}</label>						
								<select class="form-control auto-select select2-ajax" data-selected="{{ old('category_id') }}" data-table="categories" 
								data-value="id" data-display="name" data-where="3" data-href="{{ route('categories.create') }}" data-title="{{ _lang('New Item Category') }}" name="category_id" required>
									<option value="">{{ _lang('Select One') }}</option>			
									@if(old('category_id') != '')
									@foreach(\App\Models\CategoryModel::active()->where('id', old('category_id'))->get() as $category)
									<option value="{{ $category->id }}">{{ $category->name }}</option>
									@endforeach
									@endif
								</select>
							</div>
						</div>

						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Image') }} (400px X 400px)</label>						
								<input type="file" class="form-control dropify" name="image">
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Price') }} ({{ currency_symbol(request()->activeBusiness->currency) }})</label>						
								<input type="text" class="form-control" name="price" value="{{ old('price') }}" required>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Special Price') }} ({{ currency_symbol(request()->activeBusiness->currency) }})</label>						
								<input type="text" class="form-control" name="special_price" value="{{ old('special_price') }}">
							</div>
						</div>

						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Description') }}</label>						
								<textarea class="form-control" name="description">{{ old('description') }}</textarea>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Product Type') }}</label>						
								<select class="form-control auto-select" id="product_type" data-selected="{{ old('product_type',1) }}" name="product_type">
									<option value="1">{{ _lang('General Product') }}</option>
									<option value="2">{{ _lang('Variation Product') }}</option>
								</select>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Status') }}</label>						
								<select class="form-control auto-select" data-selected="{{ old('status', 1) }}" name="status" required>
									<option value="1">{{ _lang('Active') }}</option>
									<option value="0">{{ _lang('Disabled') }}</option>
								</select>
							</div>
						</div>

						<div class="col-md-12 variable-product {{ old('product_type') == '2' ? '' : 'd-none' }}">
							@if(old('product_option'))
								@foreach(old('product_option') as $product_option)
								<div class="row product-option" {{ $loop->first ? 'id="option"' : '' }}>
									<div class="col-md-6">
										<div class="form-group">
											<label class="control-label">{{ _lang('Option Name') }}</label>
											<input type="text" class="form-control product_option" name="product_option[]" value="{{ $product_option }}" placeholder="Ex - Size">
										</div>
									</div>

									<div class="col-md-6">
										<div class="form-group">
											<label class="control-label">{{ _lang('Option Value') }}</label>
											<input type="text" class="form-control product_option_value" name="product_option_value[]" value="{{ old('product_option_value.'.$loop->index) }}" placeholder="Ex - Small, Medium, Large">
										</div>
									</div>
								</div>
								@endforeach
							@else
								<div class="row product-option" id="option">
									<div class="col-md-6">
										<div class="form-group">
											<label class="control-label">{{ _lang('Option Name') }}</label>
											<input type="text" class="form-control product_option" name="product_option[]" value="" placeholder="Ex - Size">
										</div>
									</div>

									<div class="col-md-6">
										<div class="form-group">
											<label class="control-label">{{ _lang('Option Value') }}</label>
											<input type="text" class="form-control product_option_value" name="product_option_value[]" placeholder="Ex - Small, Medium, Large">
										</div>
									</div>
								</div>
							@endif

							<button type="button" class="btn btn-dark btn-xs float-right" id="add_more_option">
								<i class="ti-plus"></i> {{ _lang('Add More Option') }}
							</button>
						</div>

						<!--Product Variations-->
						<div class="col-md-12 variable-product {{ old('product_type') == 'variable_product' ? '' : 'd-none' }}">
							<div class="card">

								<div class="card-header">
									<span class="panel-title">{{ _lang('Variations Prices') }}</span>
								</div>

								<div class="card-body">
									<div class="table-responsive">
										<table class="table table-bordered" id="variations-prices-table">
											<thead>
												@if(old('variation_price'))
													@foreach(old('product_option') as $product_option)
														<th>{{ $product_option }}</th>
													@endforeach
													<th>{{ _lang('Price') }}</th>
													<th>{{ _lang('Special Price') }}</th>
													<th class="text-center">{{ _lang('Is Available') }}</th>
												@endif
											</thead>
											<tbody>
												@if(old('variation_price'))
													@foreach(old('variation_price') as $variation_price)
														<tr>
															@foreach(old('product_option') as $product_option)
																<td>{{ $product_option }}</td>
															@endforeach
															<td>
																<input type="text" name="variation_price[]" class="form-control" value="{{ old('variation_price.'.$loop->index) }}" placeholder="{{ _lang('Regular Price') }}">
															</td>
															<td>
																<input type="text" name="variation_special_price[]" class="form-control" value="{{ old('variation_special_price.'.$loop->index) }}" placeholder="{{ _lang('Special Price') }}">
															</td>
															<td class="text-center">
																<input type="hidden" name="is_available[{{ $loop->index }}]" value="0">
																<input type="checkbox" name="is_available[{{ $loop->index }}]" value="{{ old('is_available.'.$loop->index) }}">
															</td>
														</tr>
													@endforeach
												@endif
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
						</div>
				
						<div class="col-md-12 mt-2">
							<div class="form-group">
								<button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i> {{ _lang('Save') }}</button>
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


