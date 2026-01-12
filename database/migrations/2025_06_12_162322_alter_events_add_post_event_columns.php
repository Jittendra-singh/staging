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
            $table->string('waste_collected' , 255)->nullable()->default(null);
            $table->string('area_cleaned' , 255)->nullable()->default(null);
            $table->string('team_name' , 255)->nullable()->default(null);
            $table->string('post_event_notes' , 255)->nullable()->default(null);
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('waste_collected');
            $table->dropColumn('area_cleaned');
            $table->dropColumn('team_name');
            $table->dropColumn('post_event_notes');
        });
    }
};
