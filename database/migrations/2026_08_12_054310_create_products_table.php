<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->decimal('price', 10, 2)->nullable();
    $table->integer('weight')->default(1000);
    $table->integer('stock')->default(0); // <-- TAMBAHKAN KOLOM INI
    $table->string('image');
    $table->text('description')->nullable();
    $table->enum('type', ['main', 'popular'])->default('main'); 
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
