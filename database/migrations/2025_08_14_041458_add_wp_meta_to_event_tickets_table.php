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
        Schema::table('event_tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('event_tickets', 'wp_meta')) {
                // If you want to control position, add ->after('some_column') below
                $table->longText('wp_meta')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_tickets', function (Blueprint $table) {
            if (Schema::hasColumn('event_tickets', 'wp_meta')) {
                $table->dropColumn('wp_meta');
            }
        });
    }
};
