<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('installation_services', function (Blueprint $table) {
            $table->id('id_services');
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->decimal('harga', 12, 2);
            $table->unsignedSmallInteger('durasi_pemasangan')->comment('dalam menit');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('installation_services');
    }
};
