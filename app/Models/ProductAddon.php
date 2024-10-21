<?php

namespace App\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class ProductAddon extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_addons';

    protected static function boot() {
        parent::boot();
        
        static::saving(function () {
            Cache::forget('products_list'.business_id());
        });

        static::deleted(function () {
            Cache::forget('products_list'.business_id());
        });
    }

}