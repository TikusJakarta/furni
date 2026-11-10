<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model hanya-baca untuk VIEW v_product_sales
 */
class ProductSale extends Model
{
    protected $table = 'v_product_sales';

    protected $primaryKey = null;

    public $incrementing = false;

    public $timestamps = false;

    protected $casts = [
        'created_at' => 'datetime',
        'quantity'   => 'integer',
        'unit_price' => 'float',
        'line_total' => 'float',
        'is_paid'    => 'boolean',
    ];
}
