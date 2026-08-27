<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('limit_stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->index(['product_id', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('limit_stocks');
    }
};
