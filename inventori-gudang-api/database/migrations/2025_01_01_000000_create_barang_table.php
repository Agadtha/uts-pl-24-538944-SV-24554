<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang', 50)->unique();
            $table->string('nama');
            $table->string('kategori', 100)->nullable();
            $table->string('satuan', 20);
            $table->unsignedInteger('stok')->default(0);
            $table->decimal('harga', 15, 2)->default(0);
            $table->string('lokasi_rak', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
};
