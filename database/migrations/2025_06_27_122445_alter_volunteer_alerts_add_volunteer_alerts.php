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
        Schema::table('volunteer_alerts', function (Blueprint $table) {
            $table->string('alert_name')->after('volunteer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('volunteer_alerts', function (Blueprint $table) {
            $table->dropColumn('alert_name');
        });
    }
};
