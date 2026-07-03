<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('event_tickets', 'is_active')) {
                $table->boolean('is_active')->default(1)->after('minimum_quantity_to_sell');
            }

            if (!Schema::hasColumn('event_tickets', 'sell_start_time')) {
                $table->date('sell_start_time')->nullable()->after('is_active');
            }

            if (!Schema::hasColumn('event_tickets', 'sell_end_time')) {
                $table->date('sell_end_time')->nullable()->after('sell_start_time');
            }
        });
    }

    public function down(): void
    {
        Schema::table('event_tickets', function (Blueprint $table) {
            if (Schema::hasColumn('event_tickets', 'sell_end_time')) {
                $table->dropColumn('sell_end_time');
            }

            if (Schema::hasColumn('event_tickets', 'sell_start_time')) {
                $table->dropColumn('sell_start_time');
            }

            if (Schema::hasColumn('event_tickets', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};
