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
            $table->boolean('rsvp_confirmation')->nullable()->after('waiver_handler_name');
            $table->dateTime('rsvp_filled_at')->nullable()->after('rsvp_confirmation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_order_tickets', function (Blueprint $table) {
            $table->dropColumn(['rsvp_confirmation', 'rsvp_filled_at']);
        });
    }
};
