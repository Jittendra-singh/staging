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
        Schema::table('events', function (Blueprint $table) {
            $table->bigInteger('event_category_id')->nullable();
            $table->text('description')->nullable();
            $table->enum('event_reoccurance', ['once', 'daily', 'weekly', 'monthly', 'yearly'])->default('once');
            $table->dateTime('start_datetime')->nullable();
            $table->dateTime('end_datetime')->nullable();
            $table->enum('event_timezone' , ['eastern', 'central', 'mountain', 'pacific'])->default('pacific');
            $table->bigInteger('country_id')->nullable();
            $table->enum('status', ['pending', 'approved', 'cancelled'])->default('pending');
            $table->decimal('paid_ticket_price', 10, 2)->nullable();
            $table->text('ticket_description')->nullable();
            $table->integer('min_ticket')->nullable();
            $table->integer('paid_tickets')->default(0);
            $table->integer('free_tickets')->default(0);
            $table->integer('donation_tickets')->default(0);
            $table->boolean('show_remaining_tickets')->default(false);
            $table->boolean('sell_tickets_individually')->default(false);
            $table->decimal('admin_commission', 10, 2)->nullable();
            $table->string('pincode')->nullable();
            $table->string('venue')->nullable();
            $table->text('meeting_spot_details')->nullable();
            $table->text('parking_info_details')->nullable();
            $table->text('banner_image')->nullable();
            $table->text('flyer_image')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->boolean('is_private')->default(false);
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('event_category_id');
            $table->dropColumn('description');
            $table->dropColumn('event_reoccurance');
            $table->dropColumn('start_datetime');
            $table->dropColumn('end_datetime');
            $table->dropColumn('event_timezone');
            $table->dropColumn('country_id');
            $table->dropColumn('status');
            $table->dropColumn('ticket_description');
            $table->dropColumn('min_ticket');
            $table->dropColumn('paid_tickets');
            $table->dropColumn('free_tickets');
            $table->dropColumn('donation_tickets');
            $table->dropColumn('show_remaining_tickets');
            $table->dropColumn('sell_tickets_individually');
            $table->dropColumn('admin_commission');
            $table->dropColumn('pincode');
            $table->dropColumn('venue');
            $table->dropColumn('meeting_spot_details');
            $table->dropColumn('parking_info_details');
            $table->dropColumn('banner_image');
            $table->dropColumn('flyer_image');
            $table->dropColumn('latitude');
            $table->dropColumn('longitude');
            $table->dropColumn('is_private');
        });
    }
};
