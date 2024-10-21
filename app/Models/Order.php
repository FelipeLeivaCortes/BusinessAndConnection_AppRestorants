<?php

namespace App\Models;

use App\Traits\MultiTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Order extends Model {
    use MultiTenant;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'orders';

    public function items() {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function taxes() {
        return $this->hasMany(OrderTax::class, 'order_id');
    }

    public function created_by() {
        return $this->belongsTo(User::class, 'created_user_id')->withDefault();
    }

    public function upadted_by() {
        return $this->belongsTo(User::class, 'updated_user_id')->withDefault();
    }

    public function customer() {
        return $this->belongsTo(Customer::class, 'customer_id')->withDefault();
    }

    protected function grandTotal(): Attribute{
        $decimal_place = get_business_option('decimal_places', 2);

        return Attribute::make(
            get: fn($value) => number_format($value, $decimal_place, '.', ''),
        );
    }

    protected function paid(): Attribute{
        $decimal_place = get_business_option('decimal_places', 2);

        return Attribute::make(
            get: fn($value) => number_format($value, $decimal_place, '.', ''),
        );
    }

    protected function createdAt(): Attribute {
        $date_format = get_date_format();
        $time_format = get_time_format();

        return Attribute::make(
            get:fn($value) => \Carbon\Carbon::parse($value)->format("$date_format $time_format"),
        );
    }

}