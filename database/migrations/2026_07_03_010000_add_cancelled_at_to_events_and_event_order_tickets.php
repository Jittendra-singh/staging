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
        if (Schema::hasTable('events') && !Schema::hasColumn('events', 'cancelled_at')) {
            Schema::table('events', function (Blueprint $table) {
                $table->timestamp('cancelled_at')->nullable()->index()->after('deleted_at');
            });
        }

        if (Schema::hasTable('event_order_tickets') && !Schema::hasColumn('event_order_tickets', 'cancelled_at')) {
            Schema::table('event_order_tickets', function (Blueprint $table) {
                $table->timestamp('cancelled_at')->nullable()->index()->after('ticket_status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('event_order_tickets') && Schema::hasColumn('event_order_tickets', 'cancelled_at')) {
            Schema::table('event_order_tickets', function (Blueprint $table) {
                $table->dropIndex(['cancelled_at']);
                $table->dropColumn('cancelled_at');
            });
        }

        if (Schema::hasTable('events') && Schema::hasColumn('events', 'cancelled_at')) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropIndex(['cancelled_at']);
                $table->dropColumn('cancelled_at');
            });
        }
    }
};
