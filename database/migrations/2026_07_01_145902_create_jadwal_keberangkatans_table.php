<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_keberangkatans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kapal_id')
                ->constrained('kapals')
                ->cascadeOnDelete();

            $table->foreignId('rute_id')
                ->constrained('rutes')
                ->cascadeOnDelete();

            $table->date('tanggal_berangkat');
            $table->time('jam_berangkat');

            $table->unsignedInteger('kapasitas_total');
            $table->unsignedInteger('kapasitas_terpakai')->default(0);
            $table->unsignedInteger('sisa_kapasitas')->default(0);
           

            $table->enum('status', [
                'tersedia',
                'penuh',
                'selesai',
                'batal',
            ])->default('tersedia');

            $table->timestamps();

            $table->index(['tanggal_berangkat', 'jam_berangkat']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_keberangkatans');
    }
};