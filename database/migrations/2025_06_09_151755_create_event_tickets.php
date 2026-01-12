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
        Schema::create('event_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_name', 255)->nullable(false);
            $table->text('ticket_description')->nullable(false);
            $table->enum('ticket_type', ['free', 'paid'])->default('free');
            $table->decimal('ticket_price', 8, 2)->nullable()->default(0.00);
            $table->integer('available_quantity')->default(0);
            $table->integer('minimum_quantity_to_sell')->default(0);
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_tickets');
    }
};
