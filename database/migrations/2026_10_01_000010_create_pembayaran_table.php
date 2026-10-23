<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id('id_pembayaran');
            $table->foreignId('id_order')
                ->unique()
                ->constrained('orders', 'id_order')
                ->cascadeOnDelete();
            $table->string('metode', 30);
            $table->decimal('total_pembayaran', 14, 2);
            $table->enum('status', ['pending', 'diverifikasi', 'ditolak', 'berhasil'])
                ->default('pending')
                ->index();
            $table->string('bukti_pembayaran')->nullable();
            $table->dateTime('tanggal_bayar')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
