<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('event_registrations')) {
            return;
        }

        $driver = DB::getDriverName();
        if (!in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        // Add "rejected" and "expired" to status enum.
        DB::statement("ALTER TABLE event_registrations MODIFY COLUMN status ENUM('pending','active','canceled','rejected','expired') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        if (!Schema::hasTable('event_registrations')) {
            return;
        }

        $driver = DB::getDriverName();
        if (!in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        // Normalize to pending before shrinking the enum.
        DB::table('event_registrations')->whereIn('status', ['rejected', 'expired'])->update(['status' => 'pending']);

        DB::statement("ALTER TABLE event_registrations MODIFY COLUMN status ENUM('pending','active','canceled') NOT NULL DEFAULT 'pending'");
    }
};
