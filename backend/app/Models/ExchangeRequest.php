<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeRequest extends Model
{
    protected $fillable = [
        'order_item_id', 'new_product_variant_id', 'reason', 'status', 'resolution_notes'
    ];

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function newVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'new_product_variant_id');
    }
}
