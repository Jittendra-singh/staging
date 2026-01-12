<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->boolean('is_active')->default(1);
            $table->boolean('is_online')->default(1);
            $table->boolean('is_featured')->default(0);
            $table->date('registration_deadline')->default(null)->after('event_timezone');
            $table->string('video_url',  255)->nullable()->after('flyer_image');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('is_active');
            $table->dropColumn('is_online');
            $table->dropColumn('is_featured');
            $table->dropColumn('registration_deadline');
            $table->dropColumn('video_url');
        });
    }
};
