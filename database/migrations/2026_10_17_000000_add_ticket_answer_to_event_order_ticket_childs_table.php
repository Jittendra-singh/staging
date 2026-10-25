<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Raw SQL for manual execution on server if needed:
// ALTER TABLE `event_order_ticket_childs` ADD COLUMN `ticket_answer` TEXT NULL DEFAULT NULL AFTER `child_age`;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('event_order_ticket_childs', function (Blueprint $table) {
            if (!Schema::hasColumn('event_order_ticket_childs', 'ticket_answer')) {
                $table->text('ticket_answer')->nullable()->after('child_age')->comment('Answers to ticket questions for child');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_order_ticket_childs', function (Blueprint $table) {
            if (Schema::hasColumn('event_order_ticket_childs', 'ticket_answer')) {
                $table->dropColumn('ticket_answer');
            }
        });
    }
};
