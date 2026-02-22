<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_module', function (Blueprint $table) {
            $table->string('file_name')->nullable()->after('content_url');
            $table->string('mime_type')->nullable()->after('file_name');
            $table->unsignedBigInteger('file_size')->default(0)->after('mime_type');
        });
    }

    public function down(): void
    {
        Schema::table('course_module', function (Blueprint $table) {
            $table->dropColumn(['file_name', 'mime_type', 'file_size']);
        });
    }
};
