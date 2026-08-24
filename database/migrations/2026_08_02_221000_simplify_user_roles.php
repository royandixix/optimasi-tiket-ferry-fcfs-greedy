<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            DB::table('users')->where('role', 'penumpang')->update(['role' => 'user']);
            DB::table('users')->whereIn('role', ['super_admin', 'pimpinan'])->update(['role' => 'admin']);
            return;
        }

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin','admin','petugas','pimpinan','penumpang','user') NOT NULL DEFAULT 'user'");

        DB::table('users')->where('role', 'penumpang')->update(['role' => 'user']);
        DB::table('users')->whereIn('role', ['super_admin', 'pimpinan'])->update(['role' => 'admin']);

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','petugas','user') NOT NULL DEFAULT 'user'");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            DB::table('users')->where('role', 'user')->update(['role' => 'penumpang']);
            return;
        }

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','petugas','user','super_admin','pimpinan','penumpang') NOT NULL DEFAULT 'penumpang'");
        DB::table('users')->where('role', 'user')->update(['role' => 'penumpang']);
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin','admin','petugas','pimpinan','penumpang') NOT NULL DEFAULT 'penumpang'");
    }
};
