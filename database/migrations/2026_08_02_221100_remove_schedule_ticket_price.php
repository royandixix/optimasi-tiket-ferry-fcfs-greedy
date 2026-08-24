<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('jadwal_keberangkatans', 'harga_tiket')) {
            Schema::table('jadwal_keberangkatans', function (Blueprint $table): void {
                $table->dropColumn('harga_tiket');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('jadwal_keberangkatans', 'harga_tiket')) {
            Schema::table('jadwal_keberangkatans', function (Blueprint $table): void {
                $table->decimal('harga_tiket', 15, 2)->default(0);
            });
        }
    }
};
