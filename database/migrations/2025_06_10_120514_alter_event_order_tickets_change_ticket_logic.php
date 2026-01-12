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
            $table->dropColumn('ticket_type');
            $table->bigInteger('ticket_id')->nullable(false)->after('ticket_number');
            $table->decimal('price', 8, 2)->nullable()->default(0)->after('ticket_number');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_order_tickets', function (Blueprint $table) {
            $table->enum('ticket_type', ['free', 'paid'])->default('free')->after('ticket_number');
            $table->dropColumn('ticket_id');
            $table->dropColumn('price'); 
        });
    }
};
