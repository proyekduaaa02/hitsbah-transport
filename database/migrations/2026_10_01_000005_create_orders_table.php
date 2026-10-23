<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id('id_order');
            $table->foreignId('id_user')
                ->constrained('users', 'id_user')
                ->restrictOnDelete();
            $table->dateTime('tanggal_order')->index();
            $table->decimal('total_pembayaran', 14, 2)->default(0);
            $table->enum('status', ['pending', 'diproses', 'diterima', 'ditolak', 'selesai'])
                ->default('pending')
                ->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
