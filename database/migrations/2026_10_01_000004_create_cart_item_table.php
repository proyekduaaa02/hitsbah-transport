<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_item', function (Blueprint $table) {
            $table->id('id_cart_item');
            $table->foreignId('id_cart')
                ->constrained('carts', 'id')
                ->cascadeOnDelete();
            $table->foreignId('id_produk')
                ->constrained('produk', 'id_produk')
                ->cascadeOnDelete();
            $table->unsignedInteger('jumlah')->default(1);
            $table->timestamps();

            $table->unique(['id_cart', 'id_produk']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_item');
    }
};
