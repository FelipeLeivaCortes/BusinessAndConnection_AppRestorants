<?php

namespace App\Models;

use App\Traits\MultiTenant;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class ProductModel extends Model {
    use MultiTenant;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'products';

    public function scopeActive($query) {
        return $query->where('status', 1);
    }

    public function category() {
        return $this->belongsTo(CategoryModel::class, 'category_id')->withDefault();
    }

    public function product_options() {
        return $this->hasMany(ProductVariation::class, 'product_id');
    }

    public function variation_prices() {
        return $this->hasMany(ProductVariationPrice::class, 'product_id');
    }

    public function addon_products() {
        return $this->hasMany(ProductAddon::class, 'product_id');
    }

    protected static function boot() {
        parent::boot();

        static::saving(function () {
            Cache::forget('products_list' . business_id());
        });

        static::deleted(function () {
            Cache::forget('products_list' . business_id());
        });

    }
}