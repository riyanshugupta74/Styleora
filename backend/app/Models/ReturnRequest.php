<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnRequest extends Model
{
    protected $fillable = [
        'order_item_id', 'reason', 'status', 'refund_amount', 'resolution_notes'
    ];

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }
}
