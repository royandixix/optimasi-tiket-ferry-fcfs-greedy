<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayarans', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('pemesanan_tiket_id')
                ->unique()
                ->constrained('pemesanan_tikets')
                ->cascadeOnDelete();

            $table->string('kode_pembayaran')->unique();

            $table->string('metode_pembayaran', 30);

            $table->unsignedBigInteger('total_bayar');

            $table->string('bukti_transfer')->nullable();

            $table->string('status_pembayaran', 30);

            $table->timestamp('dibayar_pada')->nullable();

            $table->foreignId('diverifikasi_oleh')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('catatan_admin')->nullable();

            $table->timestamps();

            $table->index('metode_pembayaran');

            $table->index('status_pembayaran');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};