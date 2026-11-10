<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model hanya-baca untuk VIEW v_sales_report
 */
class SalesReport extends Model
{
    protected $table = 'v_sales_report';

    protected $primaryKey = 'order_id';

    public $timestamps = false;

    protected $casts = [
        'created_at'    => 'datetime',
        'order_date'    => 'date',
        'items_qty'     => 'integer',
        'shipping_cost' => 'float',
        'total_price'   => 'float',
        'is_paid'       => 'boolean',
    ];
}
