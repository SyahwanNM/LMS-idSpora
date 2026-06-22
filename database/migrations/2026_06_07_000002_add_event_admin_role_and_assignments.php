<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Extend role enum to include event_admin
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin','user','trainer','event_admin') NOT NULL DEFAULT 'user'");
        }

        // Pivot: which event_admin manages which events
        if (!Schema::hasTable('event_admin_assignments')) {
            Schema::create('event_admin_assignments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('event_id')->constrained()->onDelete('cascade');
                $table->timestamps();
                $table->unique(['user_id', 'event_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('event_admin_assignments');
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin','user','trainer') NOT NULL DEFAULT 'user'");
        }
    }
};
