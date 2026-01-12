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
        Schema::create('order_ticket_notes', function (Blueprint $table) {
            $table->id();
            $table->integer('order_ticket_id')->nullable(false);
            $table->text('notes')->nullable(false);
            $table->string('note_type', 255)->comments('default: on status change, organiser: organiser is adding external comments.');
            $table->integer('added_by')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
    */
    public function down(): void
    {
        Schema::dropIfExists('order_ticket_notes');
    }
};
