<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pemesanan_tikets', function (Blueprint $table) {
            $table->string('qr_token', 64)
                ->nullable()
                ->after('kode_pemesanan');

            $table->timestamp('digunakan_pada')
                ->nullable()
                ->after('catatan');

            $table->foreignId('divalidasi_oleh')
                ->nullable()
                ->after('digunakan_pada')
                ->constrained('users')
                ->nullOnDelete();
        });

        // Membuat QR token untuk pemesanan lama.
        DB::table('pemesanan_tikets')
            ->select('id')
            ->orderBy('id')
            ->chunkById(100, function ($pemesanans): void {
                foreach ($pemesanans as $pemesanan) {
                    DB::table('pemesanan_tikets')
                        ->where('id', $pemesanan->id)
                        ->whereNull('qr_token')
                        ->update([
                            'qr_token' => (string) Str::uuid(),
                        ]);
                }
            });

        Schema::table('pemesanan_tikets', function (Blueprint $table) {
            $table->unique('qr_token');
        });
    }

    public function down(): void
    {
        Schema::table('pemesanan_tikets', function (Blueprint $table) {
            $table->dropConstrainedForeignId('divalidasi_oleh');
            $table->dropUnique(['qr_token']);

            $table->dropColumn([
                'qr_token',
                'digunakan_pada',
            ]);
        });
    }
};