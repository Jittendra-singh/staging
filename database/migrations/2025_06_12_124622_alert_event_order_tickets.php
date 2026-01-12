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
        Schema::table('event_order_tickets', function (Blueprint $table) {
            $table->dropColumn('need_community_service_hours');
            $table->enum('community_form_status', ['pending', 'requested', 'approved'])->default('pending');
            $table->string('hours_completed', 255)->nullable()->default(null);
            $table->text('activity_description')->nullable()->default(null);
            $table->text('participation_proof')->nullable()->default(null);
            $table->text('additional_notes')->nullable()->default(null);
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_order_tickets', function (Blueprint $table) {
            $table->boolean('need_community_service_hours')->default(false);
            $table->dropColumn([
            'community_form_status',
            'hours_completed',
            'activity_description',
            'participation_proof',
            'additional_notes'
            ]);
        });
    }
}; 
