<?php

namespace App\Models;

use App\Traits\MultiTenant;
use Illuminate\Database\Eloquent\Model;

class OrderTax extends Model {
    use MultiTenant;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'order_taxes';

    protected $fillable = [
        'order_id', 'tax_id', 'name', 'rate', 'amount',
    ];

}