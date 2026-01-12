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
        Schema::table('volunteers', function (Blueprint $table) {
            $table->bigInteger('city_id')->nullable()->change();
            $table->string('pincode', 255)->nullable()->change();
            $table->string('phone', 255)->nullable()->change();
            $table->string('address_line_1', 255)->nullable()->change();
            $table->string('address_line_2', 255)->nullable()->change();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('volunteers', function (Blueprint $table) {
            $table->bigInteger('city_id')->nullable(false)->change();
            $table->string('pincode', 255)->nullable(false)->change();
            $table->string('phone', 255)->nullable(false)->change();
            $table->string('address_line_1', 255)->nullable(false)->change();
            $table->string('address_line_2', 255)->nullable(false)->change();
        });
    }
};
