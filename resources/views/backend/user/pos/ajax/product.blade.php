<div id="productDetailsModal">
    <div class="row p-2">
        <div class="col-lg-12">
            
            <div class="d-sm-flex align-items-start">
                <img class="product-image" src="{{ asset('public/uploads/media/' . $product->image) }}">
                <div class="mt-2 mt-sm-0">
                    <h4 class="product-title">{{ $product->name }}</h4>
                    <h5 class="mt-2 price">
                        <strong>
                            <span>{{ _lang('Price') }}:</span>
                            @if($product->product_type == 1)
                            <span id="price-value">{{ $product->special_price == 0 ? formatAmount($product->price, currency_symbol(request()->activeBusiness->currency)) : formatAmount($product->special_price, currency_symbol(request()->activeBusiness->currency)) }}</span>
                            @elseif($product->product_type == 2)
                                @if($product->variation_prices[0]->special_price != '' || (int) $product->variation_prices[0]->special_price != 0 )
                                <span id="price-value">{{ formatAmount($product->variation_prices[0]->special_price, currency_symbol(request()->activeBusiness->currency)) }}</span>
                                @else
                                <span id="price-value">{{ formatAmount($product->variation_prices[0]->price, currency_symbol(request()->activeBusiness->currency)) }}</span>
                                @endif
                            @endif
                        </strong>
                    </h5>
                    <p class="mt-1 product-desc">{{ $product->description }}</p>
                </div>
            </div>

            <form action="{{ route('products.get_variation_price', $product->id) }}" id="product-variation-form">
                @csrf
                <!-- Product Options -->
                @if(! $product->product_options->isEmpty())
                <h6 class="mt-4 mb-2"><strong>{{ _lang('Select Options') }}</strong></h6>   
                <table class="table table-striped">
                    <tbody>
                    @foreach($product->product_options as $product_option)	
                        <tr>
                            <td class="pl-2">
                                <h6>{{ $product_option->name }}</h6>
                            </td>
                            <td>
                                <div class="product_options">
                                    <select name="product_option[]" class="form-control select_product_option">
                                        @foreach($product_option->items as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                @endif
            
                @if($product->addon_products->count() > 0)
                <div class="extras mt-4">
                    <h6><strong>{{ _lang('Addon Items') }}</strong></h6>
                    <div class="table-responsive mt-2">
                        <table class="table table-striped">
                            @foreach($product->addon_products as $addon_product)
                            <tr>
                                <td class="pl-2">{{ $addon_product->name }}</td>
                                <td><b>+ {{ formatAmount($addon_product->price, currency_symbol(request()->activeBusiness->currency)) }}</b></td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" class="primary product_addon" name="product_addon_id[]" value="{{ $addon_product->id }}">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                            </tr>
                            @endforeach
                        </table>
                    </div>   
                </div>
                @endif
            </form>

            <form action="{{ route('pos.add_to_cart', [$product->id, $_GET['table_id']]) }}" class="mt-3" id="add-to-cart-form">    
                <div class="product-qnt">
                    <input type="number" name="quantity" id="quantity" value="1" min="1" placeholder="{{ _lang('Quantity') }}" required>
                </div>
                <button type="submit" class="btn-cart"><i class="fas fa-shopping-basket"></i> {{ _lang('Add to Cart') }}</button>
            </form>
        </div>
    </div>
</div>