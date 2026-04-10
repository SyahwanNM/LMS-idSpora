<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('manual_payments')) {
            return;
        }

        $driver = DB::getDriverName();
        // This project uses MySQL/MariaDB in Laragon; ENUM alteration is driver-specific.
        if (!in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        // Add "expired" to status enum if not already present.
        // NOTE: Laravel Schema builder can't reliably alter ENUM without doctrine/dbal.
        DB::statement("ALTER TABLE manual_payments MODIFY COLUMN status ENUM('pending','settled','rejected','expired') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        if (!Schema::hasTable('manual_payments')) {
            return;
        }

        $driver = DB::getDriverName();
        if (!in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        // Normalize any existing expired rows to rejected before shrinking the enum.
        DB::table('manual_payments')->where('status', 'expired')->update(['status' => 'rejected']);

        DB::statement("ALTER TABLE manual_payments MODIFY COLUMN status ENUM('pending','settled','rejected') NOT NULL DEFAULT 'pending'");
    }
};
