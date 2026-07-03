<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_translations', function (Blueprint $table) {
            $table->id();
            $table->string('model_type', 191);
            $table->string('model_id', 64);
            $table->string('field', 100);
            $table->string('locale', 10);
            $table->string('source_hash', 64);
            $table->longText('source_text')->nullable();
            $table->longText('translated_text');
            $table->string('provider', 50)->nullable();
            $table->timestamps();

            $table->unique(
                ['model_type', 'model_id', 'field', 'locale', 'source_hash'],
                'content_translations_unique_source'
            );
            $table->index(['model_type', 'model_id', 'field', 'locale'], 'content_translations_lookup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_translations');
    }
};
