<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hasil_optimasis', function (Blueprint $table) {
            $table->id();

            $table->foreignId('jadwal_id')
                ->constrained('jadwal_keberangkatans')
                ->cascadeOnDelete();

            $table->enum('metode', [
                'fcfs',
                'greedy',
            ]);

            $table->unsignedInteger('total_pemesanan')->default(0);
            $table->unsignedInteger('total_tiket_diminta')->default(0);
            $table->unsignedInteger('total_tiket_diterima')->default(0);
            $table->unsignedInteger('total_tiket_ditolak')->default(0);

            $table->unsignedInteger('kapasitas_kapal')->default(0);
            $table->unsignedInteger('kapasitas_terpakai')->default(0);

            $table->decimal('load_factor', 5, 2)->default(0);
            $table->decimal('waktu_proses_ms', 10, 4)->default(0);

            $table->foreignId('diproses_oleh')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['jadwal_id', 'metode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hasil_optimasis');
    }
};