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
        Schema::create('event_orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('event_id');
            $table->bigInteger('creator_id');
            $table->string('order_number')->unique();
            $table->integer('total_tickets');
            $table->decimal('per_ticket_price', 10, 2);
            $table->decimal('tax_pert', 5, 2);
            $table->decimal('tax_amount', 10, 2);
            $table->decimal('discount_pert', 5, 2)->nullable();
            $table->decimal('discount_amount', 10, 2)->nullable();
            $table->decimal('sub_total', 10, 2);
            $table->decimal('grand_total', 10, 2);
            $table->string('payment_type');
            $table->enum('payment_status' , ['pending', 'paid'])->default('pending');
            $table->string('payment_transaction')->nullable();
            $table->string('payment_intent')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_orders');
    }
};
