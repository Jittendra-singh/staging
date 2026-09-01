<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventWaitlistsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('event_waitlists')) {
            Schema::create('event_waitlists', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('event_id');
                $table->unsignedBigInteger('ticket_id');
                $table->string('first_name');
                $table->string('last_name');
                $table->string('email');
                $table->string('status', 20)->default('waiting');
                $table->timestamp('joined_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('event_waitlists');
    }
}
