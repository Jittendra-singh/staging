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
        if (Schema::hasTable('events')) {
            Schema::table('events', function (Blueprint $table) {
                if (!Schema::hasColumn('events', 'is_send_reminder_email')) {
                    $table->boolean('is_send_reminder_email')->default(true)->after('banner_image_orientation');
                }
                if (!Schema::hasColumn('events', 'reminder_custom_note')) {
                    $table->text('reminder_custom_note')->nullable()->after('is_send_reminder_email');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('events')) {
            Schema::table('events', function (Blueprint $table) {
                if (Schema::hasColumn('events', 'is_send_reminder_email')) {
                    $table->dropColumn('is_send_reminder_email');
                }
                if (Schema::hasColumn('events', 'reminder_custom_note')) {
                    $table->dropColumn('reminder_custom_note');
                }
            });
        }
    }
};
