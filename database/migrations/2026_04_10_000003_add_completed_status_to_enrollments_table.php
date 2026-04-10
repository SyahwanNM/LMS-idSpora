<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // NOTE: enrollments.status is an ENUM in MySQL/MariaDB.
        // Add 'completed' to support certificate issuance & downloads.
        DB::statement("ALTER TABLE enrollments MODIFY COLUMN status ENUM('pending','active','completed','expired','canceled') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        // Normalize unsupported value before removing it from ENUM.
        DB::statement("UPDATE enrollments SET status = 'active' WHERE status = 'completed'");
        DB::statement("ALTER TABLE enrollments MODIFY COLUMN status ENUM('pending','active','expired','canceled') NOT NULL DEFAULT 'pending'");
    }
};
