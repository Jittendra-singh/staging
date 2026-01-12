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
            $table->boolean('checkin')->default(false)->after('extra_notes')->comment('Indicates if the ticket has been checked in');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_order_tickets', function (Blueprint $table) {
            $table->dropColumn('checkin');
        });
    }
};
