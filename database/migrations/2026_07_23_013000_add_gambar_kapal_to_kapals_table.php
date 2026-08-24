<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kapals', function (Blueprint $table) {
            $table->json('gambar_kapal')
                ->nullable()
                ->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('kapals', function (Blueprint $table) {
            $table->dropColumn('gambar_kapal');
        });
    }
};
