<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Raw SQL for reference / manual execution:
// ALTER TABLE `event_order_ticket_childs` ADD COLUMN `is_participating` TINYINT(1) NOT NULL DEFAULT 0 AFTER `child_age`;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('event_order_ticket_childs', function (Blueprint $table) {
            if (!Schema::hasColumn('event_order_ticket_childs', 'is_participating')) {
                $table->boolean('is_participating')->default(0)->after('child_age');
            }
        });
    }
};
