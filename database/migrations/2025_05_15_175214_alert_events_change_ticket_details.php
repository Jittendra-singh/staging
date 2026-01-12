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
            $table->string('ticket_name' , 255)->nullable()->default(null);
            $table->text('ticket_description' )->nullable()->default(null)->after('ticket_name')->change();
            $table->boolean('show_ticket_description')->dafault(1)->after('ticket_description');
            $table->boolean('show_remaining_tickets')->dafault(1)->after('show_ticket_description')->change();
            $table->boolean('sell_tickets_individually')->dafault(1)->after('show_remaining_tickets')->change();
            $table->integer('paid_tickets')->after('ticket_description')->change();
            $table->integer('free_tickets')->after('ticket_description')->change();
            $table->integer('donation_tickets')->after('ticket_description')->change();
            $table->integer('min_ticket')->after('ticket_description')->change();
            $table->integer('max_ticket')->after('min_ticket');
            $table->boolean('is_private_ticket')->after('ticket_description')->default(0);
            $table->datetime('ticket_sell_start_time')->after('is_private_ticket')->default(null);
            $table->datetime('ticket_sell_end_time')->after('ticket_sell_start_time')->default(null);
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'ticket_name',
                'show_ticket_description',
                'max_ticket',
                'is_private_ticket',
                'ticket_sell_start_time',
                'ticket_sell_end_time',
            ]);
        });
    }
};
