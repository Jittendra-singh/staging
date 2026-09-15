<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Raw SQL for reference / manual execution:
// ALTER TABLE `event_order_ticket_childs` 
// ADD COLUMN `price` DECIMAL(8, 2) NULL DEFAULT 0.00 AFTER `is_participating`,
// ADD COLUMN `ticket_base_price` DECIMAL(8, 2) NULL DEFAULT 0.00 AFTER `price`,
// ADD COLUMN `ticket_platform_fee` DECIMAL(8, 2) NULL DEFAULT 0.00 AFTER `ticket_base_price`,
// ADD COLUMN `ticket_processing_fee` DECIMAL(8, 2) NULL DEFAULT 0.00 AFTER `ticket_platform_fee`,
// ADD COLUMN `tikcet_org_payout` DECIMAL(8, 2) NULL DEFAULT 0.00 AFTER `ticket_processing_fee`;

// Single-line SQL:
// ALTER TABLE `event_order_ticket_childs` ADD COLUMN `price` DECIMAL(8, 2) NULL DEFAULT 0.00 AFTER `is_participating`, ADD COLUMN `ticket_base_price` DECIMAL(8, 2) NULL DEFAULT 0.00 AFTER `price`, ADD COLUMN `ticket_platform_fee` DECIMAL(8, 2) NULL DEFAULT 0.00 AFTER `ticket_base_price`, ADD COLUMN `ticket_processing_fee` DECIMAL(8, 2) NULL DEFAULT 0.00 AFTER `ticket_platform_fee`, ADD COLUMN `tikcet_org_payout` DECIMAL(8, 2) NULL DEFAULT 0.00 AFTER `ticket_processing_fee`;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('event_order_ticket_childs', function (Blueprint $table) {
            if (!Schema::hasColumn('event_order_ticket_childs', 'price')) {
                $table->decimal('price', 8, 2)->nullable()->default(0.00)->after('is_participating');
            }
            if (!Schema::hasColumn('event_order_ticket_childs', 'ticket_base_price')) {
                $table->decimal('ticket_base_price', 8, 2)->nullable()->default(0.00)->after('price');
            }
            if (!Schema::hasColumn('event_order_ticket_childs', 'ticket_platform_fee')) {
                $table->decimal('ticket_platform_fee', 8, 2)->nullable()->default(0.00)->after('ticket_base_price');
            }
            if (!Schema::hasColumn('event_order_ticket_childs', 'ticket_processing_fee')) {
                $table->decimal('ticket_processing_fee', 8, 2)->nullable()->default(0.00)->after('ticket_platform_fee');
            }
            if (!Schema::hasColumn('event_order_ticket_childs', 'tikcet_org_payout')) {
                $table->decimal('tikcet_org_payout', 8, 2)->nullable()->default(0.00)->after('ticket_processing_fee');
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
            foreach (['price', 'ticket_base_price', 'ticket_platform_fee', 'ticket_processing_fee', 'tikcet_org_payout'] as $column) {
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
