<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('release_notes')) {
            Schema::create('release_notes', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('version', 100);
                $table->unsignedTinyInteger('type')->default(1);
                $table->text('description');
                $table->boolean('status')->default(false);
                $table->date('release_date');
                $table->timestamp('published_at')->nullable()->index();
                $table->timestamps();

                $table->index(['status', 'release_date']);
            });
        }

        if (Schema::hasTable('volunteers') && !Schema::hasColumn('volunteers', 'release_note_last_seen_at')) {
            Schema::table('volunteers', function (Blueprint $table) {
                $table->timestamp('release_note_last_seen_at')->nullable()->after('email_verified_at');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('volunteers') && Schema::hasColumn('volunteers', 'release_note_last_seen_at')) {
            Schema::table('volunteers', function (Blueprint $table) {
                $table->dropColumn('release_note_last_seen_at');
            });
        }

        Schema::dropIfExists('release_notes');
    }
};
