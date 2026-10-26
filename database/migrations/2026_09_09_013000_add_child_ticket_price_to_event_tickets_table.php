<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Raw SQL for reference / manual execution:
// ALTER TABLE `event_tickets` ADD COLUMN `child_ticket_price` DECIMAL(8, 2) NULL DEFAULT 0.00 AFTER `ticket_price`;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('event_tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('event_tickets', 'child_ticket_price')) {
                $table->decimal('child_ticket_price', 8, 2)->nullable()->default(0.00)->after('ticket_price');
            }
        });
    }

};
