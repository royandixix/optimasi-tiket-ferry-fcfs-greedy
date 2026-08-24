<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('jadwal_keberangkatans', 'harga_tiket')) {
            Schema::table('jadwal_keberangkatans', function (Blueprint $table): void {
                $table->decimal('harga_tiket', 15, 2)
                    ->default(0)
                    ->after('jam_berangkat');
            });
        }

        if (! Schema::hasColumn('pemesanan_tikets', 'harga_satuan')) {
            Schema::table('pemesanan_tikets', function (Blueprint $table): void {
                $table->decimal('harga_satuan', 15, 2)
                    ->default(0)
                    ->after('jumlah_tiket');
            });
        }

        if (! Schema::hasColumn('pemesanan_tikets', 'total_harga')) {
            Schema::table('pemesanan_tikets', function (Blueprint $table): void {
                $table->decimal('total_harga', 15, 2)
                    ->default(0)
                    ->after('harga_satuan');
            });
        }

        DB::table('pemesanan_tikets')
            ->select(['id', 'jadwal_id', 'jumlah_tiket'])
            ->orderBy('id')
            ->chunkById(200, function ($rows): void {
                $jadwalIds = collect($rows)
                    ->pluck('jadwal_id')
                    ->filter()
                    ->unique()
                    ->values();

                $prices = DB::table('jadwal_keberangkatans')
                    ->whereIn('id', $jadwalIds)
                    ->pluck('harga_tiket', 'id');

                foreach ($rows as $row) {
                    $hargaSatuan = (float) ($prices[$row->jadwal_id] ?? 0);
                    $jumlahTiket = max((int) $row->jumlah_tiket, 1);

                    DB::table('pemesanan_tikets')
                        ->where('id', $row->id)
                        ->update([
                            'harga_satuan' => $hargaSatuan,
                            'total_harga' => $hargaSatuan * $jumlahTiket,
                        ]);
                }
            });
    }

    public function down(): void
    {
        if (Schema::hasColumn('pemesanan_tikets', 'total_harga')) {
            Schema::table('pemesanan_tikets', function (Blueprint $table): void {
                $table->dropColumn('total_harga');
            });
        }

        if (Schema::hasColumn('pemesanan_tikets', 'harga_satuan')) {
            Schema::table('pemesanan_tikets', function (Blueprint $table): void {
                $table->dropColumn('harga_satuan');
            });
        }

        if (Schema::hasColumn('jadwal_keberangkatans', 'harga_tiket')) {
            Schema::table('jadwal_keberangkatans', function (Blueprint $table): void {
                $table->dropColumn('harga_tiket');
            });
        }
    }
};
