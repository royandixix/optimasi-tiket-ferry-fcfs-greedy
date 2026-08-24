<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE users
            MODIFY COLUMN role ENUM(
                'super_admin',
                'admin',
                'petugas',
                'pimpinan',
                'penumpang'
            ) NOT NULL DEFAULT 'penumpang'
        ");
    }

    public function down(): void
    {
        DB::table('users')
            ->where('role', 'petugas')
            ->update([
                'role' => 'admin',
            ]);

        DB::statement("
            ALTER TABLE users
            MODIFY COLUMN role ENUM(
                'super_admin',
                'admin',
                'pimpinan',
                'penumpang'
            ) NOT NULL DEFAULT 'penumpang'
        ");
    }
};