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
        Schema::table('event_orders', function (Blueprint $table) {
            $table->string('billing_first_name', 255)->nullable();
            $table->string('billing_last_name', 255)->nullable();
            $table->string('billing_email', 255)->nullable();
            $table->string('billing_phone', 255)->nullable();
            $table->string('billing_company', 255)->nullable();
            $table->string('billing_pincode', 255)->nullable();
            $table->unsignedBigInteger('billing_pincode_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_orders', function (Blueprint $table) {
            $table->dropColumn([
                'billing_first_name',
                'billing_last_name',
                'billing_email',
                'billing_phone',
                'billing_company',
                'billing_pincode',
                'billing_pincode_id',
            ]);
        });
    }
};
