<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('organizer_id')->nullable()->index();
            $table->unsignedBigInteger('volunteer_id')->nullable()->index();
            $table->string('context_type', 80)->default('contact_organizer')->index();
            $table->unsignedBigInteger('context_id')->nullable()->index();
            $table->string('subject')->nullable();
            $table->string('status', 40)->default('active')->index();
            $table->unsignedBigInteger('latest_message_id')->nullable();
            $table->timestamp('last_message_at')->nullable()->index();
            $table->string('reply_token', 80)->nullable()->unique();
            $table->string('reply_alias')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('organizer_id')->references('id')->on('volunteers')->nullOnDelete();
            $table->foreign('volunteer_id')->references('id')->on('volunteers')->nullOnDelete();
            $table->index(['organizer_id', 'volunteer_id', 'context_type']);
        });

        Schema::create('conversation_notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('volunteer_id')->unique();
            $table->string('default_delivery', 30)->default('immediate');
            $table->unsignedInteger('daily_email_limit')->default(50);
            $table->unsignedTinyInteger('digest_hour')->default(8);
            $table->timestamp('muted_until')->nullable();
            $table->timestamps();

            $table->foreign('volunteer_id')->references('id')->on('volunteers')->cascadeOnDelete();
        });

        Schema::create('conversation_participants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conversation_id');
            $table->unsignedBigInteger('volunteer_id');
            $table->string('role', 30)->default('volunteer')->index();
            $table->unsignedBigInteger('last_read_message_id')->nullable();
            $table->timestamp('last_read_at')->nullable();
            $table->timestamp('archived_at')->nullable()->index();
            $table->timestamp('muted_until')->nullable();
            $table->string('notification_preference', 30)->nullable();
            $table->timestamps();

            $table->foreign('conversation_id')->references('id')->on('conversations')->cascadeOnDelete();
            $table->foreign('volunteer_id')->references('id')->on('volunteers')->cascadeOnDelete();
            $table->unique(['conversation_id', 'volunteer_id']);
            $table->index(['volunteer_id', 'archived_at']);
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conversation_id');
            $table->unsignedBigInteger('sender_id')->nullable();
            $table->string('sender_type', 30)->default('volunteer');
            $table->longText('body');
            $table->string('source', 30)->default('web')->index();
            $table->string('email_message_id')->nullable()->index();
            $table->text('email_references')->nullable();
            $table->unsignedBigInteger('inbound_email_log_id')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('conversation_id')->references('id')->on('conversations')->cascadeOnDelete();
            $table->foreign('sender_id')->references('id')->on('volunteers')->nullOnDelete();
            $table->index(['conversation_id', 'created_at']);
        });

        Schema::create('message_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('message_id');
            $table->string('path');
            $table->string('original_name');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('message_id')->references('id')->on('messages')->cascadeOnDelete();
        });

        Schema::create('pending_contact_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organizer_id');
            $table->unsignedBigInteger('volunteer_id')->nullable();
            $table->unsignedBigInteger('conversation_id')->nullable();
            $table->string('token_hash', 64)->unique();
            $table->string('name');
            $table->string('email')->index();
            $table->string('phone')->nullable();
            $table->text('message');
            $table->timestamp('expires_at')->index();
            $table->timestamp('consumed_at')->nullable();
            $table->timestamps();

            $table->foreign('organizer_id')->references('id')->on('volunteers')->cascadeOnDelete();
            $table->foreign('volunteer_id')->references('id')->on('volunteers')->nullOnDelete();
            $table->foreign('conversation_id')->references('id')->on('conversations')->nullOnDelete();
        });

        Schema::create('message_delivery_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conversation_id');
            $table->unsignedBigInteger('message_id')->nullable();
            $table->unsignedBigInteger('recipient_id');
            $table->string('delivery_type', 30)->default('immediate')->index();
            $table->string('status', 30)->default('pending')->index();
            $table->timestamp('sent_at')->nullable()->index();
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('conversation_id')->references('id')->on('conversations')->cascadeOnDelete();
            $table->foreign('message_id')->references('id')->on('messages')->nullOnDelete();
            $table->foreign('recipient_id')->references('id')->on('volunteers')->cascadeOnDelete();
            $table->index(['recipient_id', 'status', 'delivery_type']);
        });

        Schema::create('inbound_email_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conversation_id')->nullable();
            $table->unsignedBigInteger('message_id')->nullable();
            $table->string('from_email')->nullable()->index();
            $table->string('to_email')->nullable();
            $table->string('message_id_header')->nullable()->index();
            $table->string('status', 30)->default('received')->index();
            $table->text('error_message')->nullable();
            $table->json('raw_payload')->nullable();
            $table->timestamps();

            $table->foreign('conversation_id')->references('id')->on('conversations')->nullOnDelete();
            $table->foreign('message_id')->references('id')->on('messages')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inbound_email_logs');
        Schema::dropIfExists('message_delivery_logs');
        Schema::dropIfExists('pending_contact_messages');
        Schema::dropIfExists('message_attachments');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversation_participants');
        Schema::dropIfExists('conversation_notification_preferences');
        Schema::dropIfExists('conversations');
    }
};
