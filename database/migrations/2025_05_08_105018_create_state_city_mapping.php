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
        Schema::create('countries', function (Blueprint $table) {
            $table->id('country_id');
            $table->string('country_name', 100)->unique();
            $table->char('country_code', 2)->unique();
            $table->timestamps();
        });
        // Create states table
        Schema::create('states', function (Blueprint $table) {
            $table->id('state_id');
            $table->string('state_name', 100);
            $table->bigInteger('country_id');
            $table->string('state_code', 10)->nullable();
            $table->timestamps();
        });
        // Create cities table
        Schema::create('cities', function (Blueprint $table) {
            $table->id('city_id');
            $table->string('city_name', 100);
            $table->bigInteger('state_id');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
        Schema::dropIfExists('states');
        Schema::dropIfExists('cities');
    }
};
