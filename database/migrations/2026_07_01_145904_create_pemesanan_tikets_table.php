<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemesanan_tikets', function (Blueprint $table) {
            $table->id();

            $table->string('kode_pemesanan')->unique();

            $table->foreignId('penumpang_id')
                ->constrained('penumpangs')
                ->cascadeOnDelete();

            $table->foreignId('jadwal_id')
                ->constrained('jadwal_keberangkatans')
                ->cascadeOnDelete();

            $table->unsignedInteger('jumlah_tiket')->default(1);
            $table->dateTime('waktu_pemesanan');

            $table->enum('status_pemesanan', [
                'pending',
                'diterima',
                'ditolak',
            ])->default('pending');

            $table->enum('metode_alokasi', [
                'fcfs',
                'greedy',
            ])->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('catatan')->nullable();

            $table->timestamps();

            $table->index(['jadwal_id', 'status_pemesanan']);
            $table->index('waktu_pemesanan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemesanan_tikets');
    }
};