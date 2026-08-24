<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alokasi_tikets', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pemesanan_tiket_id')
                ->constrained('pemesanan_tikets')
                ->cascadeOnDelete();

            $table->foreignId('jadwal_id')
                ->constrained('jadwal_keberangkatans')
                ->cascadeOnDelete();

            $table->enum('metode', [
                'fcfs',
                'greedy',
            ]);

            $table->unsignedInteger('jumlah_dialokasikan')->default(0);
            $table->integer('nilai_prioritas')->default(0);

            $table->unsignedInteger('sisa_kapasitas_sebelum')->default(0);
            $table->unsignedInteger('sisa_kapasitas_sesudah')->default(0);

            $table->enum('status_alokasi', [
                'diterima',
                'ditolak',
            ]);

            $table->foreignId('diproses_oleh')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique(['pemesanan_tiket_id', 'metode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alokasi_tikets');
    }
};