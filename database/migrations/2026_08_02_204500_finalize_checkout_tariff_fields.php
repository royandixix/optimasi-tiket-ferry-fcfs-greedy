<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('pemesanan_tikets', 'jenis_tarif')) {
            Schema::table('pemesanan_tikets', function (Blueprint $table): void {
                $table->string('jenis_tarif', 80)->nullable();
            });
        }

        if (! Schema::hasColumn('pemesanan_tikets', 'tarif_label')) {
            Schema::table('pemesanan_tikets', function (Blueprint $table): void {
                $table->string('tarif_label')->nullable();
            });
        }

        if (! Schema::hasColumn('pemesanan_tikets', 'satuan')) {
            Schema::table('pemesanan_tikets', function (Blueprint $table): void {
                $table->string('satuan', 20)->nullable();
            });
        }

        if (! Schema::hasColumn('pemesanan_tikets', 'harga_satuan')) {
            Schema::table('pemesanan_tikets', function (Blueprint $table): void {
                $table->unsignedBigInteger('harga_satuan')->default(0);
            });
        }

        if (! Schema::hasColumn('pemesanan_tikets', 'total_harga')) {
            Schema::table('pemesanan_tikets', function (Blueprint $table): void {
                $table->unsignedBigInteger('total_harga')->default(0);
            });
        }

        DB::table('pemesanan_tikets')
            ->select([
                'id',
                'jumlah_tiket',
                'jenis_tarif',
                'tarif_label',
                'satuan',
                'harga_satuan',
                'total_harga',
            ])
            ->orderBy('id')
            ->chunkById(100, function ($rows): void {
                foreach ($rows as $row) {
                    $tariffs = [
                        'penumpang_dewasa' => ['Penumpang Dewasa', 'orang', 58300],
                        'penumpang_bayi' => ['Penumpang Bayi', 'orang', 6000],
                        'golongan_1' => ['Kendaraan Golongan I', 'unit', 66000],
                        'golongan_2' => ['Kendaraan Golongan II', 'unit', 115000],
                        'golongan_3' => ['Kendaraan Golongan III', 'unit', 233200],
                        'golongan_4_penumpang' => ['Golongan IV A - Kendaraan Penumpang', 'unit', 888800],
                        'golongan_4_barang' => ['Golongan IV B - Kendaraan Barang', 'unit', 922900],
                        'golongan_5_penumpang' => ['Golongan V A - Kendaraan Penumpang', 'unit', 1598400],
                        'golongan_5_barang' => ['Golongan V B - Kendaraan Barang', 'unit', 1652500],
                        'golongan_6_penumpang' => ['Golongan VI A - Kendaraan Penumpang', 'unit', 2153000],
                        'golongan_6_barang' => ['Golongan VI B - Kendaraan Barang', 'unit', 2158800],
                        'golongan_7' => ['Kendaraan Golongan VII', 'unit', 3252300],
                        'golongan_8' => ['Kendaraan Golongan VIII', 'unit', 3556800],
                        'golongan_9' => ['Kendaraan Golongan IX', 'unit', 4364800],
                    ];

                    $code = $row->jenis_tarif ?: 'penumpang_dewasa';
                    [$defaultLabel, $defaultUnit, $defaultPrice] =
                        $tariffs[$code] ?? $tariffs['penumpang_dewasa'];

                    $quantity = max((int) $row->jumlah_tiket, 1);
                    $price = (int) $row->harga_satuan > 0
                        ? (int) $row->harga_satuan
                        : $defaultPrice;

                    DB::table('pemesanan_tikets')
                        ->where('id', $row->id)
                        ->update([
                            'jenis_tarif' => $code,
                            'tarif_label' => $row->tarif_label ?: $defaultLabel,
                            'satuan' => $row->satuan ?: $defaultUnit,
                            'harga_satuan' => $price,
                            'total_harga' => (int) $row->total_harga > 0
                                ? (int) $row->total_harga
                                : $price * $quantity,
                        ]);
                }
            });
    }

    public function down(): void
    {
        // Data harga transaksi sengaja tidak dihapus saat rollback.
    }
};
