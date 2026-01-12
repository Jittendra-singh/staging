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
            $table->enum('waiver_form_type', ['text', 'pdf', 'external_link','default'])->nullable(true)->default(null);
            $table->text('waiver_form_content')->default(null);
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('waiver_form_type');
            $table->dropColumn('waiver_form_content');
        });
    }
};
