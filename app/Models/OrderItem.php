<?php

namespace App\Models;

use App\Traits\MultiTenant;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model {
    use MultiTenant;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'order_items';

    protected $fillable = [
        'order_id', 'product_id', 'cart_id', 'product_name', 'description', 'quantity', 'unit_cost', 'sub_total',
    ];

    public function product() {
        return $this->belongsTo(ProductModel::class, 'product_id')->withDefault();
    }


}