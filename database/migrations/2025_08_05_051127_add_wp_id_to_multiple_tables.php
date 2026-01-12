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
        $tables = ['events', 'event_orders', 'event_tickets', 'volunteers'];

        foreach ($tables as $table) {
            if (!Schema::hasColumn($table, 'wp_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->unsignedBigInteger('wp_id')->nullable();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['events', 'event_orders', 'event_tickets', 'volunteers'];

        foreach ($tables as $table) {
            if (Schema::hasColumn($table, 'wp_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropColumn('wp_id');
                });
            }
        }
    }
};
