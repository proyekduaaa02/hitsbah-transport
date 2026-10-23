<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services_booking', function (Blueprint $table) {
            $table->id('id_services_booking');
            $table->foreignId('id_user')
                ->constrained('users', 'id_user')
                ->restrictOnDelete();
            $table->foreignId('id_order')
                ->constrained('orders', 'id_order')
                ->cascadeOnDelete();
            $table->foreignId('id_services')
                ->constrained('installation_services', 'id_services')
                ->restrictOnDelete();
            $table->dateTime('jadwal_pemasangan')->index();
            $table->enum('status_pemasangan', ['menunggu', 'dijadwalkan', 'diproses', 'selesai', 'dibatalkan'])
                ->default('menunggu')
                ->index();
            $table->decimal('harga', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services_booking');
    }
};
