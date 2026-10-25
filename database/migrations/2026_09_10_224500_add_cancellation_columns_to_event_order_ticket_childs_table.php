<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Raw SQL for reference / manual execution:
// ALTER TABLE `event_order_ticket_childs` 
// ADD COLUMN `refund_status` TINYINT NULL DEFAULT 1 AFTER `tikcet_org_payout`,
// ADD COLUMN `cancelled_at` TIMESTAMP NULL DEFAULT NULL AFTER `refund_status`,
// ADD COLUMN `is_amt_donated` TINYINT(1) NOT NULL DEFAULT 0 AFTER `cancelled_at`;

// Single-line SQL:
// ALTER TABLE `event_order_ticket_childs` ADD COLUMN `refund_status` TINYINT NULL DEFAULT 1 AFTER `tikcet_org_payout`, ADD COLUMN `cancelled_at` TIMESTAMP NULL DEFAULT NULL AFTER `refund_status`, ADD COLUMN `is_amt_donated` TINYINT(1) NOT NULL DEFAULT 0 AFTER `cancelled_at`;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('event_order_ticket_childs', function (Blueprint $table) {
            if (!Schema::hasColumn('event_order_ticket_childs', 'refund_status')) {
                $table->tinyInteger('refund_status')->nullable()->default(1)->after('tikcet_org_payout');
            }
            if (!Schema::hasColumn('event_order_ticket_childs', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->default(null)->after('refund_status');
            }
            if (!Schema::hasColumn('event_order_ticket_childs', 'is_amt_donated')) {
                $table->tinyInteger('is_amt_donated')->default(0)->after('cancelled_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_order_ticket_childs', function (Blueprint $table) {
            $columnsToDrop = [];
            foreach (['refund_status', 'cancelled_at', 'is_amt_donated'] as $column) {
                if (Schema::hasColumn('event_order_ticket_childs', $column)) {
                    $columnsToDrop[] = $column;
                }
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
