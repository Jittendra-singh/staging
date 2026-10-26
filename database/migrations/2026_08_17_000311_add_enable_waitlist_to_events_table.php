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
        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'allow_donations')) {
                $table->boolean('allow_donations')->default(0);
            }

            if (!Schema::hasColumn('events', 'enable_waitlist')) {
                $table->boolean('enable_waitlist')->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'enable_waitlist')) {
                $table->dropColumn('enable_waitlist');
            }
        });
    }
};
