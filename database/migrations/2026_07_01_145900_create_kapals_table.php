<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kapals', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kapal')->unique()->nullable();
            $table->string('nama_kapal');
            $table->unsignedInteger('kapasitas_penumpang');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kapals');
    }
};