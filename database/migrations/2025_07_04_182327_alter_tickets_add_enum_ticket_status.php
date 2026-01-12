<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('event_order_tickets', function (Blueprint $table) {
            // $table->string('ticket_status')->after('volunteer_id');
            DB::statement("ALTER TABLE event_order_tickets MODIFY COLUMN ticket_status ENUM('pending','approved','cancelled') NOT NULL DEFAULT 'pending'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_order_tickets', function (Blueprint $table) {
            DB::statement("ALTER TABLE event_order_tickets MODIFY COLUMN ticket_status ENUM('approved','cancelled') NOT NULL DEFAULT 'approved'");
        });
    }
};
