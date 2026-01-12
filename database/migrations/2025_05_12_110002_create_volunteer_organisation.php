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
        Schema::create('volunteer_organisation', function (Blueprint $table) {
            $table->id();
            $table->string('volunteer_id')->nullable();
            $table->string('landing_slug');
            $table->text('organisation_logo')->nullable();
            $table->string('organisation_name' , 255)->nullable();
            $table->text('short_description')->nullable();
            $table->string('website', 255)->nullable();
            $table->string('instagram', 255)->nullable();
            $table->string('facebook', 255)->nullable();
            $table->string('linkedin', 255)->nullable();
            $table->string('twitter', 255)->nullable();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('volunteer_organisation');
    }
};
