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
        Schema::table('event_order_tickets', function (Blueprint $table) {
            $table->boolean('is_under_18')->default(false)->after('waiver_filled_at');
            $table->string('waiver_handler_name',255)->nullable(true)->default(null)->after('waiver_filled_at');
            $table->string('waiver_handler_email',255)->nullable(true)->default(null)->after('waiver_filled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void 
    {
        Schema::table('event_order_tickets', function (Blueprint $table) {
            $table->dropColumn('is_under_18');
            $table->dropColumn('waiver_handler_name');
            $table->dropColumn('waiver_handler_email');
        });
    }
};
