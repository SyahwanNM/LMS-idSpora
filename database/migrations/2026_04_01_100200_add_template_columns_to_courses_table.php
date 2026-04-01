<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'template_id')) {
                $table->foreignId('template_id')
                    ->nullable()
                    ->after('category_id')
                    ->constrained('course_templates')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('courses', 'template_version')) {
                $table->unsignedInteger('template_version')
                    ->nullable()
                    ->after('template_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'template_id')) {
                $table->dropConstrainedForeignId('template_id');
            }

            if (Schema::hasColumn('courses', 'template_version')) {
                $table->dropColumn('template_version');
            }
        });
    }
};
