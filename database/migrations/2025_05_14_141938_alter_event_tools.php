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
        Schema::table('event_tools', function (Blueprint $table) {
            $table->dropColumn('extra_notes');
            $table->dropColumn('quantity');
            $table->dropColumn('should_bring');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_tools', function (Blueprint $table) {
            $table->text('extra_notes', 255);
            $table->integer('quantity');
            $table->boolean('should_bring');
        });
    }
};
