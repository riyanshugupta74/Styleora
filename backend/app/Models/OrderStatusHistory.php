<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatusHistory extends Model
{
    protected $fillable = [
        'order_id', 'status', 'notes', 'created_by'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
