<?php

namespace App\Models;

use App\Traits\MultiTenant;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model {
    use MultiTenant;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transactions';

    public function transaction_category() {
        return $this->belongsTo(TransactionCategory::class, 'transaction_category_id')->withDefault(function (TransactionCategory $category, Transaction $transaction) {
            if ($transaction->ref_id != null && $transaction->ref_type == 'purchase') {
                $category->name  = _lang('Purchase / Bill Payment');
                $category->color = '#ff4757';
            } else {
                $category->name  = _lang('Uncategorized');
                $category->color = '#ced6e0';
            }
        });
    }

    public function vendor() {
        return $this->belongsTo(Vendor::class, 'vendor_id')->withDefault();
    }

    public function customer() {
        return $this->belongsTo(Customer::class, 'customer_id')->withDefault();
    }

    public function staff() {
        return $this->belongsTo(Employee::class, 'employee_id')->withDefault();
    }

    protected function amount(): Attribute {
        $decimal_place = get_business_option('decimal_places', 2);

        return Attribute::make(
            get: fn(string $value) => number_format($value, $decimal_place, '.', ''),
        );
    }

    public function purchase() {
        return $this->belongsTo(Purchase::class, 'ref_id')->withDefault();
    }

    public function created_by() {
        return $this->belongsTo(User::class, 'created_user_id')->withDefault();
    }

    public function updated_by() {
        return $this->belongsTo(User::class, 'updated_user_id')->withDefault(['name' => _lang('N/A')]);
    }

    protected function createdAt(): Attribute {
        $date_format = get_date_format();
        $time_format = get_time_format();

        return Attribute::make(
            get: fn(string $value) => \Carbon\Carbon::parse($value)->format("$date_format $time_format"),
        );
    }

    protected function updatedAt(): Attribute {
        $date_format = get_date_format();
        $time_format = get_time_format();

        return Attribute::make(
            get: fn(string $value) => \Carbon\Carbon::parse($value)->format("$date_format $time_format"),
        );
    }

    protected function transDate(): Attribute {
        $date_format = get_date_format();
        $time_format = get_time_format();

        return Attribute::make(
            get: fn(string $value) => \Carbon\Carbon::parse($value)->format("$date_format $time_format"),
        );
    }

    protected static function booted(): void {
        static::deleting(function (Transaction $transaction) {
            if ($transaction->ref_type == 'purchase') {
                $purcahse       = Purchase::find($transaction->ref_id);
                $purcahse->paid = $purcahse->paid - $transaction->ref_amount;

                if ($purcahse->transactions->count() == 0) {
                    $purcahse->paid = 0;
                }
                if ($purcahse->paid == 0) {
                    $purcahse->status = 0; //Unpaid
                }
                if ($purcahse->paid > 0) {
                    $purcahse->status = 1; //Partially Paid
                }
                if ($purcahse->paid >= $purcahse->grand_total) {
                    $purcahse->status = 2; //Paid
                }
                $purcahse->save();
            }
        });
    }
}