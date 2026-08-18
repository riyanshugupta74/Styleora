<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'order_number', 'address_id', 'subtotal', 'discount', 
        'shipping', 'coupon_discount', 'total', 'status', 'payment_method', 
        'payment_status', 'notes'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }
    
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getTimelineStatusAttribute()
    {
        $stages = ['placed', 'confirmed', 'packed', 'shipped', 'out_for_delivery', 'delivered'];
        $currentStatus = strtolower($this->status);
        
        $timeline = [];
        $passed = true;
        
        if (in_array($currentStatus, ['cancelled', 'return_requested', 'returned', 'exchange_requested', 'exchanged'])) {
             // Handle special terminal statuses separately in the view
             return $timeline; 
        }

        foreach ($stages as $stage) {
            $timeline[$stage] = [
                'label' => ucwords(str_replace('_', ' ', $stage)),
                'completed' => $passed,
                'current' => $stage === $currentStatus
            ];
            
            if ($stage === $currentStatus) {
                $passed = false;
            }
        }
        
        return $timeline;
    }
}
