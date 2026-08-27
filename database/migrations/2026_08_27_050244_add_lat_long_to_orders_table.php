<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('country');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('company_name')->nullable();
            $table->string('address');
            $table->string('apartment')->nullable();
            $table->string('state_country');
            $table->string('postal_zip');
            $table->string('email');
            $table->string('phone');
            $table->string('payment_method');
            $table->text('order_notes')->nullable();
            $table->decimal('total_price', 12, 2);
            // Kolom Latitude & Longitude untuk Leaflet.js
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->decimal('shipping_cost', 12, 2)->default(0);
            $table->string('shipping_courier')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};