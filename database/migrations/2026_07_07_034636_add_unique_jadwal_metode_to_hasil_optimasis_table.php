<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bersihkan data dobel lebih dulu agar unique index tidak gagal dibuat
        $duplicates = DB::table('hasil_optimasis')
            ->select('jadwal_id', 'metode', DB::raw('MIN(id) as keep_id'))
            ->groupBy('jadwal_id', 'metode')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $duplicate) {
            DB::table('hasil_optimasis')
                ->where('jadwal_id', $duplicate->jadwal_id)
                ->where('metode', $duplicate->metode)
                ->where('id', '!=', $duplicate->keep_id)
                ->delete();
        }

        Schema::table('hasil_optimasis', function (Blueprint $table) {
            $table->unique(
                ['jadwal_id', 'metode'],
                'hasil_optimasis_jadwal_id_metode_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('hasil_optimasis', function (Blueprint $table) {
            $table->dropUnique('hasil_optimasis_jadwal_id_metode_unique');
        });
    }
};