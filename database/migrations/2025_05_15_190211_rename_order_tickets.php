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
        Schema::rename('table_order_tickets', 'event_order_tickets');
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('event_order_tickets', 'table_order_tickets');
    }
};
