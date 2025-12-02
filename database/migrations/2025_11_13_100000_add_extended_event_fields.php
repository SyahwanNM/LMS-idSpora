<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Existing base columns: id, title, image, speaker, description, location, price, event_time, event_date, timestamps
            // Add missing operational document paths
            if(!Schema::hasColumn('events','vbg_path')) $table->string('vbg_path')->nullable()->after('image');
            if(!Schema::hasColumn('events','certificate_path')) $table->string('certificate_path')->nullable()->after('vbg_path');
            if(!Schema::hasColumn('events','attendance_path')) $table->string('attendance_path')->nullable()->after('certificate_path');

            // Classification / metadata
            if(!Schema::hasColumn('events','materi')) $table->string('materi')->nullable()->after('speaker');
            if(!Schema::hasColumn('events','jenis')) $table->string('jenis')->nullable()->after('materi');
            if(!Schema::hasColumn('events','level')) $table->string('level')->nullable()->after('jenis');

            // Terms & conditions
            if(!Schema::hasColumn('events','terms_and_conditions')) $table->longText('terms_and_conditions')->nullable()->after('description');

            // Communication link
            if(!Schema::hasColumn('events','whatsapp_link')) $table->string('whatsapp_link')->nullable()->after('location');

            // Discount fields
            if(!Schema::hasColumn('events','discount_percentage')) $table->unsignedInteger('discount_percentage')->default(0)->after('price');
            if(!Schema::hasColumn('events','discount_until')) $table->date('discount_until')->nullable()->after('discount_percentage');

            // Extended timing
            if(!Schema::hasColumn('events','event_time_end')) $table->time('event_time_end')->nullable()->after('event_time');

            // Value proposition / benefit
            if(!Schema::hasColumn('events','benefit')) $table->text('benefit')->nullable()->after('terms_and_conditions');

            // Location links
            if(!Schema::hasColumn('events','maps_url')) $table->string('maps_url')->nullable()->after('location');
            if(!Schema::hasColumn('events','zoom_link')) $table->string('zoom_link')->nullable()->after('maps_url');

            // Structured JSON data for schedule & expenses
            if(!Schema::hasColumn('events','schedule_json')) $table->json('schedule_json')->nullable()->after('zoom_link');
            if(!Schema::hasColumn('events','expenses_json')) $table->json('expenses_json')->nullable()->after('schedule_json');

            // Soft deletes support (model uses SoftDeletes)
            if(!Schema::hasColumn('events','deleted_at')) $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Drop ONLY the columns we introduced (guard if exist)
            $dropCols = [
                'vbg_path','certificate_path','attendance_path','materi','jenis','level','terms_and_conditions','whatsapp_link',
                'discount_percentage','discount_until','event_time_end','benefit','maps_url','zoom_link','schedule_json','expenses_json'
            ];
            foreach($dropCols as $col){ if(Schema::hasColumn('events',$col)) $table->dropColumn($col); }
            if(Schema::hasColumn('events','deleted_at')) $table->dropSoftDeletes();
        });
    }
};
