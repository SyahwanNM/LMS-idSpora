<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('events')) {
            return;
        }

        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'is_published')) {
                $table->boolean('is_published')->default(false);
            }
            if (!Schema::hasColumn('events', 'published_at')) {
                $table->timestamp('published_at')->nullable();
            }
        });

        // Add index separately to avoid issues when columns already exist.
        if (Schema::hasColumn('events', 'is_published')) {
            try {
                Schema::table('events', function (Blueprint $table) {
                    $table->index('is_published', 'events_is_published_index');
                });
            } catch (\Throwable $e) {
                // ignore (index may already exist)
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('events')) {
            return;
        }

        Schema::table('events', function (Blueprint $table) {
            try {
                $table->dropIndex('events_is_published_index');
            } catch (\Throwable $e) {
                // ignore
            }

            if (Schema::hasColumn('events', 'published_at')) {
                $table->dropColumn('published_at');
            }
            if (Schema::hasColumn('events', 'is_published')) {
                $table->dropColumn('is_published');
            }
        });
    }
};
