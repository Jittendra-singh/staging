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
        Schema::create('volunteers', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 255);
            $table->string('last_name', 255);
            $table->string('email_address', 255);
            $table->timestamp('email_verified_at')->nullable();
            $table->text('password');
            $table->boolean('is_newsletter')->default(true);
            $table->string('phone', 255); 
            $table->string('address_line_1', 255);
            $table->string('address_line_2', 255);
            $table->bigInteger('city_id');
            $table->string('pincode', 255);
            $table->boolean('status')->default(true);
            $table->boolean('is_organiser')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('volunteers');
    }
};
