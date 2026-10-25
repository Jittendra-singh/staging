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
        if (Schema::hasTable('events') && !Schema::hasColumn('events', 'banner_image_orientation')) {
            Schema::table('events', function (Blueprint $table) {
                $table->string('banner_image_orientation', 20)->default('landscape')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('events') && Schema::hasColumn('events', 'banner_image_orientation')) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropColumn('banner_image_orientation');
            });
        }
    }
};
