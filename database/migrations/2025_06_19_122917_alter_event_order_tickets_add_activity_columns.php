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
            $table->boolean('status')->default(true)->change();
            $table->enum('ticket_status', ['approved','cancelled'])->default('approved');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_order_tickets', function (Blueprint $table) {
            $table->string('status', 255)->change();
            $table->dropColumn(['ticket_status']);
        });
    }
};
