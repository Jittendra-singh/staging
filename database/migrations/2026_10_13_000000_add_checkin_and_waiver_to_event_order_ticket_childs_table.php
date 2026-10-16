<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Raw SQL for manual execution on server if needed:
// ALTER TABLE `event_order_ticket_childs` 
// ADD COLUMN `checkin` TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_participating`,
// ADD COLUMN `checkin_dt` TIMESTAMP NULL DEFAULT NULL AFTER `checkin`,
// ADD COLUMN `waiver_filled_at` TIMESTAMP NULL DEFAULT NULL AFTER `checkin_dt`,
// ADD COLUMN `signature` TEXT NULL DEFAULT NULL AFTER `waiver_filled_at`,
// ADD COLUMN `community_form_pdf` VARCHAR(255) NULL DEFAULT NULL AFTER `signature`,
// ADD COLUMN `community_form_status` VARCHAR(50) NULL DEFAULT NULL AFTER `community_form_pdf`;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('event_order_ticket_childs', function (Blueprint $table) {
            if (!Schema::hasColumn('event_order_ticket_childs', 'checkin')) {
                $table->tinyInteger('checkin')->default(0)->after('is_participating');
            }
            if (!Schema::hasColumn('event_order_ticket_childs', 'checkin_dt')) {
                $table->timestamp('checkin_dt')->nullable()->after('checkin');
            }
            if (!Schema::hasColumn('event_order_ticket_childs', 'waiver_filled_at')) {
                $table->timestamp('waiver_filled_at')->nullable()->after('checkin_dt');
            }
            if (!Schema::hasColumn('event_order_ticket_childs', 'signature')) {
                $table->text('signature')->nullable()->after('waiver_filled_at');
            }
            if (!Schema::hasColumn('event_order_ticket_childs', 'community_form_pdf')) {
                $table->string('community_form_pdf', 255)->nullable()->after('signature');
            }
            if (!Schema::hasColumn('event_order_ticket_childs', 'community_form_status')) {
                $table->string('community_form_status', 50)->nullable()->after('community_form_pdf');
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
            foreach ([
                'checkin',
                'checkin_dt',
                'waiver_filled_at',
                'signature',
                'community_form_pdf',
                'community_form_status',
            ] as $column) {
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
