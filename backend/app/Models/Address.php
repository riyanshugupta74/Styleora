<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'full_name', 'phone', 'address_line_1', 'address_line_2', 
        'city', 'state', 'pincode', 'country', 'is_default'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}