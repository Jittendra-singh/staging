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
        Schema::create('volunteer_alerts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('volunteer_id');
            $table->string('zipcode', 10);
            $table->boolean('status')->default(false);
            $table->enum('email_frequency', ['daily', 'weekly', 'monthly'])->default('weekly');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('volunteer_alerts');
    }
};
