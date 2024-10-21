<?php

namespace App\Models;

use App\Models\BusinessSetting;
use App\Models\BusinessType;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Business extends Model {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'business';

    protected static function booted(): void {
        if (auth()->check()) {
            static::addGlobalScope('business_id', function (Builder $builder) {
                if (request()->has('activeBusiness')) {
                    $builder->whereIn('business.id', request()->businessList->pluck('id'));
                }
            });
        }
    }

    public function scopeActive($query) {
        return $query->where('status', 1);
    }

    public function scopeOwner($query) {
        return $query->where('user_id', auth()->id());
    }

    public function business_type() {
        return $this->belongsTo(BusinessType::class, 'business_type_id')->withDefault();
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id')->withDefault();
    }

    public function systemSettings() {
        return $this->hasMany(BusinessSetting::class, 'business_id');
    }

    public function users() {
        return $this->belongsToMany(User::class, 'business_users')->withPivot('owner_id', 'role_id')->withTimestamps();
    }

    public function role() {
        return $this->belongsToMany(Role::class, 'business_users')->withTimestamps();
    }

    public static function createDefaultBusiness() {
        DB::beginTransaction();

        $business           = new Business();
        $business->name     = 'Untitled';
        $business->user_id  = auth()->id();
        $business->logo     = 'default/default-company-logo.png';
        $business->status   = 1;
        $business->default  = 1;
        $business->country  = 'United States of America';
        $business->currency = get_option('currency', 'USD');
        $business->save();

        $business->users()->attach($business->user_id, ['owner_id' => $business->user_id, 'is_active' => 1]);

        DB::commit();

        return $business;

    }
}