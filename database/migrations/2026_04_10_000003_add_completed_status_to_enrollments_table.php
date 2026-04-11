<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('enrollments')) {
            return;
        }

        $driver = DB::getDriverName();
        if (!in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        // NOTE: enrollments.status is an ENUM in MySQL/MariaDB.
        // Add 'completed' to support certificate issuance & downloads.
        DB::statement("ALTER TABLE enrollments MODIFY COLUMN status ENUM('pending','active','completed','expired','canceled') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        if (!Schema::hasTable('enrollments')) {
            return;
        }

        $driver = DB::getDriverName();
        if (!in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        // Normalize unsupported value before removing it from ENUM.
        DB::statement("UPDATE enrollments SET status = 'active' WHERE status = 'completed'");
        DB::statement("ALTER TABLE enrollments MODIFY COLUMN status ENUM('pending','active','expired','canceled') NOT NULL DEFAULT 'pending'");
    }
};
