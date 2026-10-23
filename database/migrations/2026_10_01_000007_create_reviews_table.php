<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id('id_reviews');
            $table->foreignId('id_user')
                ->constrained('users', 'id_user')
                ->cascadeOnDelete();
            $table->foreignId('id_produk')
                ->constrained('produk', 'id_produk')
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('rating'); // 1 - 5 (divalidasi di ReviewRequest)
            $table->text('komen');
            $table->text('balasan_admin')->nullable();
            $table->dateTime('tanggal_balasan')->nullable();
            $table->timestamps();

            $table->unique(['id_user', 'id_produk']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
