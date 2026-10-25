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
        if (Schema::hasTable('events')) {
            Schema::table('events', function (Blueprint $table) {
                if (!Schema::hasColumn('events', 'allow_donations')) {
                    $table->boolean('allow_donations')->default(false);
                }
                if (!Schema::hasColumn('events', 'allow_refund')) {
                    $table->boolean('allow_refund')->default(false);
                }
                if (!Schema::hasColumn('events', 'charge_bear_by')) {
                    $table->tinyInteger('charge_bear_by')->default(0);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('events')) {
            Schema::table('events', function (Blueprint $table) {
                if (Schema::hasColumn('events', 'allow_donations')) {
                    $table->dropColumn('allow_donations');
                }
                if (Schema::hasColumn('events', 'allow_refund')) {
                    $table->dropColumn('allow_refund');
                }
                if (Schema::hasColumn('events', 'charge_bear_by')) {
                    $table->dropColumn('charge_bear_by');
                }
            });
        }
    }
};
