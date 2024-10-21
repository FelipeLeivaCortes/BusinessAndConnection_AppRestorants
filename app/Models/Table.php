<?php

namespace App\Models;

use App\Traits\MultiTenant;
use Illuminate\Database\Eloquent\Model;

class Table extends Model {
    use MultiTenant;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tables';

    public function hall() {
        return $this->belongsTo(Hall::class, 'hall_id')->withDefault();
    }
}