<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'type', 'value', 'min_order', 'max_discount', 
        'start_date', 'expiry_date', 'usage_limit', 'used_count', 'status'
    ];
    
    protected $casts = [
        'start_date' => 'datetime',
        'expiry_date' => 'datetime',
    ];

    public function usages()
    {
        return $this->hasMany(CouponUsage::class);
    }
}