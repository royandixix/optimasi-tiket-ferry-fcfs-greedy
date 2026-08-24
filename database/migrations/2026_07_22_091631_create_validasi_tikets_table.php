<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('validasi_tikets', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pemesanan_tiket_id')
                ->nullable()
                ->constrained('pemesanan_tikets')
                ->nullOnDelete();

            $table->foreignId('divalidasi_oleh')
                ->constrained('users')
                ->restrictOnDelete();

            $table->enum('metode_validasi', [
                'qr_code',
                'kode_booking',
            ]);

            $table->string('nilai_diperiksa', 100);

            $table->enum('status_validasi', [
                'berhasil',
                'gagal',
            ]);

            $table->string('alasan_gagal')->nullable();
            $table->timestamp('waktu_validasi');

            $table->timestamps();

            $table->index([
                'divalidasi_oleh',
                'waktu_validasi',
            ]);

            $table->index([
                'pemesanan_tiket_id',
                'status_validasi',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('validasi_tikets');
    }
};