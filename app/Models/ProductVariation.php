<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model {

    public $timestamps = false;

    protected $fillable = ['name'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_variations';

    public function items() {
        return $this->hasMany(ProductVariationItem::class, 'variation_id');
    }

}