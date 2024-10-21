<?php

namespace App\Models;

use App\Traits\MultiTenant;
use Illuminate\Database\Eloquent\Model;

class Hall extends Model {
    use MultiTenant;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'halls';

    public function scopeActive($query) {
        return $query->where('status', 1);
    }

    public function tables() {
        return $this->hasMany(Table::class, 'hall_id');
    }
}