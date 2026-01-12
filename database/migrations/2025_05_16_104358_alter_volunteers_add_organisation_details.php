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
        Schema::table('volunteers', function (Blueprint $table) {
            $table->string('organisation_logo')->nullable()->default(null);
            $table->string('organisation_name')->nullable()->default(null);
            $table->string('organisation_landing_slug')->nullable()->default(null);
            $table->text('organisation_short_description')->nullable()->default(null);
            $table->string('organisation_website')->nullable()->default(null);
            $table->string('organisation_instagram')->nullable()->default(null);
            $table->string('organisation_facebook')->nullable()->default(null);
            $table->string('organisation_linkedin')->nullable()->default(null);
            $table->string('organisation_twitter')->nullable()->default(null);
            $table->timestamp('organisation_updated_at')->nullable()->default(null);
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('volunteers', function (Blueprint $table) {
            //
        });
    }
};
