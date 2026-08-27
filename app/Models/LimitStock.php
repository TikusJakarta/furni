<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LimitStock extends Model
{
    protected $fillable = [
        'user_id', 
        'product_id', 
        'quantity', 
        'order_id', 
        'expires_at'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
