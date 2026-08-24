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
                $table->string('jenis_tarif', 80)
                    ->default('penumpang_dewasa')
                    ->after('jumlah_tiket')
                    ->index();
            });
        }

        if (! Schema::hasColumn('pemesanan_tikets', 'tarif_label')) {
            Schema::table('pemesanan_tikets', function (Blueprint $table): void {
                $table->string('tarif_label', 180)
                    ->nullable()
                    ->after('jenis_tarif');
            });
        }

        if (! Schema::hasColumn('pemesanan_tikets', 'harga_satuan')) {
            Schema::table('pemesanan_tikets', function (Blueprint $table): void {
                $table->decimal('harga_satuan', 15, 2)
                    ->default(0)
                    ->after('tarif_label');
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
            ->orderBy('id')
            ->chunkById(200, function ($rows): void {
                foreach ($rows as $row) {
                    $quantity = max((int) $row->jumlah_tiket, 1);

                    if (
                        blank($row->jenis_tarif)
                        || blank($row->tarif_label)
                        || (float) $row->harga_satuan <= 0
                        || (float) $row->total_harga <= 0
                    ) {
                        DB::table('pemesanan_tikets')
                            ->where('id', $row->id)
                            ->update([
                                'jenis_tarif' => 'penumpang_dewasa',
                                'tarif_label' => 'Penumpang - Dewasa',
                                'harga_satuan' => 58300,
                                'total_harga' => 58300 * $quantity,
                            ]);
                    }
                }
            });
    }

    public function down(): void
    {
        if (Schema::hasColumn('pemesanan_tikets', 'jenis_tarif')) {
            Schema::table('pemesanan_tikets', function (Blueprint $table): void {
                $table->dropIndex(['jenis_tarif']);
                $table->dropColumn('jenis_tarif');
            });
        }

        if (Schema::hasColumn('pemesanan_tikets', 'tarif_label')) {
            Schema::table('pemesanan_tikets', function (Blueprint $table): void {
                $table->dropColumn('tarif_label');
            });
        }
    }
};
