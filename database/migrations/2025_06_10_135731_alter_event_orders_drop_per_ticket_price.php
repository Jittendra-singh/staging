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
        Schema::table('event_orders', function (Blueprint $table) {
            $table->dropColumn('per_ticket_price');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_orders', function (Blueprint $table) {
            $table->decimal('per_ticket_price', 10, 2)->after('total_tickets');
        });
    }
};
