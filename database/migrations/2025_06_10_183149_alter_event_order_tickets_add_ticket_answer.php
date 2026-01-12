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
            $table->text('ticket_answer')
                ->nullable()
                ->after('checkin')
                ->comment('Answers to ticket questions');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_order_tickets', function (Blueprint $table) {
            $table->dropColumn('ticket_answer');
        });
    }
};
