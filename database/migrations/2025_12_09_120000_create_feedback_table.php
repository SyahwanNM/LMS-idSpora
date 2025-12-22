<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('feedback')) {
            Schema::create('feedback', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('event_id');
                $table->unsignedBigInteger('user_id');
                $table->unsignedTinyInteger('rating'); // 1-5 overall event rating
                $table->unsignedTinyInteger('speaker_rating')->nullable(); // optional speaker rating 1-5
                $table->unsignedTinyInteger('committee_rating')->nullable(); // optional committee rating 1-5
                $table->text('comment');
                $table->timestamps();

                $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->index(['event_id', 'user_id']);
            });
        } else {
            // If table exists, ensure columns are present
            Schema::table('feedback', function (Blueprint $table) {
                if (!Schema::hasColumn('feedback', 'speaker_rating')) {
                    $table->unsignedTinyInteger('speaker_rating')->nullable()->after('rating');
                }
                if (!Schema::hasColumn('feedback', 'committee_rating')) {
                    $table->unsignedTinyInteger('committee_rating')->nullable()->after('speaker_rating');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
