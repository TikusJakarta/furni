<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\LimitStock;
use App\Models\Review;

class Product extends Model
{
    protected $guarded = ['id'];

    public function getEffectiveStockAttribute()
    {
        $reservedStock = LimitStock::where('product_id', $this->id)
            ->where('expires_at', '>', now())
            ->sum('quantity');

        return max(0, $this->stock - $reservedStock);
    }


    // Relasi ke model Review
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function averageRating()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }
}