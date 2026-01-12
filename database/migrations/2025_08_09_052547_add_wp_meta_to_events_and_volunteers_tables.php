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
        // Add wp_meta to events table
        Schema::table('events', function (Blueprint $table) {
            $table->longText('wp_meta')->nullable(); 
        });

        // Add wp_meta to volunteers table
        Schema::table('volunteers', function (Blueprint $table) {
            $table->longText('wp_meta')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove wp_meta from events table
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('wp_meta');
        });

        // Remove wp_meta from volunteers table
        Schema::table('volunteers', function (Blueprint $table) {
            $table->dropColumn('wp_meta');
        });
    }
};
