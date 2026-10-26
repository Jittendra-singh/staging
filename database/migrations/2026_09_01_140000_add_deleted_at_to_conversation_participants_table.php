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
        Schema::table('conversation_participants', function (Blueprint $table) {
            if (!Schema::hasColumn('conversation_participants', 'deleted_at')) {
                $column = $table->timestamp('deleted_at')->nullable();
                if (Schema::hasColumn('conversation_participants', 'muted_until')) {
                    $column->after('muted_until');
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversation_participants', function (Blueprint $table) {
            if (Schema::hasColumn('conversation_participants', 'deleted_at')) {
                $table->dropColumn('deleted_at');
            }
        });
    }
};
