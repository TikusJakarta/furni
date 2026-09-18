<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'country',
        'first_name',
        'last_name',
        'company_name',
        'address',
        'apartment',
        'state_country',
        'postal_zip',
        'email',
        'phone',
        'payment_method',
        'order_notes',
        'shipping_cost',
        'shipping_courier',
        'total_price',
        'latitude',
        'longitude',
        'status',
        'is_protected',    
        'protection_fee',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi utama
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function items()
    {
        return $this->orderItems();
    }
}