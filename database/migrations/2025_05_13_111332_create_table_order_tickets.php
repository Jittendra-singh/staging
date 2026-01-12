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
        Schema::create('table_order_tickets', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id');
            $table->bigInteger('event_id')->nullable();
            $table->bigInteger('volunteer_id');
            $table->string('ticket_number' , 255)->unique();
            $table->enum('ticket_type', ['paid', 'free', 'donation']);
            $table->string('status' , 255);
            $table->text('extra_notes')->nullable();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_order_tickets');
    }
};
