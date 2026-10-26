<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Raw SQL for reference / manual execution in Database (phpMyAdmin / MySQL CLI):
// ALTER TABLE `volunteers` ADD COLUMN `is_child` TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_organiser`;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('volunteers', function (Blueprint $table) {
            if (!Schema::hasColumn('volunteers', 'is_child')) {
                $table->boolean('is_child')
                      ->default(0)
                      ->after('is_organiser');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('volunteers', function (Blueprint $table) {
            if (Schema::hasColumn('volunteers', 'is_child')) {
                $table->dropColumn('is_child');
            }
        });
    }
};
