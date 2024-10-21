<?php

namespace App\Models;

use App\Traits\MultiTenant;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class CategoryModel extends Model {
    use MultiTenant;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categories';

    public function scopeActive($query) {
        return $query->where('status', 1);
    }

    protected static function boot() {
        parent::boot();

        static::saving(function () {
            Cache::forget('category_list'.business_id());
        });

        static::deleted(function () {
            Cache::forget('category_list'.business_id());
        });
    }
}