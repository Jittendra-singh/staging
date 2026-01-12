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
            $table->dropColumn([
                'ticket_name',
                'ticket_description',
                'free_tickets',
                'donation_tickets',
                'paid_tickets',
                'paid_ticket_price',
                'min_ticket',
                'max_ticket',
            ]);
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('ticket_name')->nullable();
            $table->text('ticket_description')->nullable();
            $table->integer('free_tickets')->default(0);
            $table->integer('donation_tickets')->default(0);
            $table->integer('paid_tickets')->default(0);
            $table->decimal('paid_ticket_price', 10, 2)->nullable();
            $table->integer('min_ticket')->default(1);
            $table->integer('max_ticket')->default(1);
        });
    }
};
