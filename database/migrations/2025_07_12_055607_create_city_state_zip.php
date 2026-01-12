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
        Schema::create('city_state_zips', function (Blueprint $table) {
            $table->id();
            $table->string('city');
            $table->string('state');
            $table->string('state_full_name');
            $table->string('zipcode', 10);
            $table->decimal('lat', 10, 7);
            $table->decimal('long', 10, 7);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('city_state_zip');
    }
};
