<?php

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Pengolahan data (gabung pesanan + item + produk + pelanggan, hitung jumlah item,
 */
return new class extends Migration {
    public function up(): void
    {
        $orders   = (new Order)->getTable();
        $items    = (new Order)->items()->getRelated()->getTable();
        $products = (new Product)->getTable();
        $users    = (new User)->getTable();

        $paid = "'" . implode("','", ['paid', 'packing', 'shipping', 'shipped', 'completed']) . "'";

        $name = "TRIM(CONCAT(COALESCE(o.first_name, ''), ' ', COALESCE(o.last_name, '')))";
        $join = '';

        if (Schema::hasColumn($orders, 'user_id')) {
            $name = "COALESCE(NULLIF($name, ''), u.name)";
            $join = "LEFT JOIN `$users` u ON u.id = o.user_id";
        }

        $unitPrice = Schema::hasColumn($items, 'price') ? 'COALESCE(i.price, p.price)' : 'p.price';

        DB::statement("
            CREATE OR REPLACE VIEW v_sales_report AS
            SELECT
                o.id                     AS order_id,
                o.created_at             AS created_at,
                DATE(o.created_at)       AS order_date,
                $name                    AS customer_name,
                o.email                  AS customer_email,
                COALESCE(q.qty, 0)       AS items_qty,
                COALESCE(o.shipping_cost, 0) AS shipping_cost,
                o.total_price            AS total_price,
                o.status                 AS status,
                CASE WHEN o.status IN ($paid) THEN 1 ELSE 0 END AS is_paid
            FROM `$orders` o
            LEFT JOIN (
                SELECT order_id, SUM(quantity) AS qty
                FROM `$items`
                GROUP BY order_id
            ) q ON q.order_id = o.id
            $join
        ");

        DB::statement("
            CREATE OR REPLACE VIEW v_product_sales AS
            SELECT
                o.id                     AS order_id,
                o.created_at             AS created_at,
                o.status                 AS status,
                CASE WHEN o.status IN ($paid) THEN 1 ELSE 0 END AS is_paid,
                p.id                     AS product_id,
                p.name                   AS product_name,
                i.quantity               AS quantity,
                $unitPrice               AS unit_price,
                i.quantity * $unitPrice  AS line_total
            FROM `$items` i
            JOIN `$orders`   o ON o.id = i.order_id
            JOIN `$products` p ON p.id = i.product_id
        ");
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS v_product_sales');
        DB::statement('DROP VIEW IF EXISTS v_sales_report');
    }
};
