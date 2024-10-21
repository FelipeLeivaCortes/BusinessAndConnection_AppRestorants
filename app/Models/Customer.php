<?php

namespace App\Models;

use App\Traits\MultiTenant;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model {
    use MultiTenant;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'customers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'phone', 'city', 'state', 'address',
    ];

    public function orders() {
        return $this->hasMany(Order::class, 'customer_id');
    }

}