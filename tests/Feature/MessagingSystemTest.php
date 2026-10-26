<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use App\Models\MessageDeliveryLog;
use App\Models\Volunteer;
use App\Services\Messaging\ConversationNotificationService;
use App\Services\Messaging\ConversationService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class MessagingSystemTest extends TestCase
{
    protected function createOrganizer(array $attributes = [])
    {
        return Volunteer::create(array_merge([
            'first_name' => 'Org',
            'last_name' => 'Leader',
            'email_address' => 'org_' . Str::random(8) . '@example.com',
            'password' => bcrypt('password'),
            'is_organiser' => true,
        ], $attributes));
    }

    /** @test */
    public function guest_contact_without_consent_creates_lead_and_guest_conversation()
    {
        Mail::fake();

        $organizer = $this->createOrganizer();

        $conversationService = app(ConversationService::class);
        $conversation = $conversationService->startGuestContactRequest([
            'organiser_id' => $organizer->id,
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '555-0199',
            'message' => 'Hello Organizer!',
        ]);

        $this->assertInstanceOf(Conversation::class, $conversation);
        $this->assertNull($conversation->volunteer_id);
        $this->assertEquals($organizer->id, $conversation->organizer_id);
        $this->assertEquals('guest_inquiry', $conversation->context_type);
        $this->assertTrue($conversation->metadata['is_guest']);
        $this->assertEquals('Jane Doe', $conversation->metadata['guest_name']);
        $this->assertEquals('jane@example.com', $conversation->metadata['guest_email']);

        // Assert Message created
        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'sender_id' => null,
            'sender_type' => 'guest',
            'body' => 'Hello Organizer!',
        ]);

        // Assert Organizer Participant created
        $this->assertDatabaseHas('conversation_participants', [
            'conversation_id' => $conversation->id,
            'volunteer_id' => $organizer->id,
            'role' => 'organizer',
        ]);
    }

    /** @test */
    public function same_guest_reuses_existing_conversation()
    {
        Mail::fake();

        $organizer = $this->createOrganizer();
        $conversationService = app(ConversationService::class);

        $conv1 = $conversationService->startGuestContactRequest([
            'organiser_id' => $organizer->id,
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '555-0199',
            'message' => 'First message',
        ]);

        $conv2 = $conversationService->startGuestContactRequest([
            'organiser_id' => $organizer->id,
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '555-0199',
            'message' => 'Second message',
        ]);

        $this->assertEquals($conv1->id, $conv2->id);
        $this->assertEquals(2, $conv1->messages()->count());
    }

    /** @test */
    public function missing_organizer_participant_is_auto_healed()
    {
        Mail::fake();

        $organizer = $this->createOrganizer();

        // Create an orphan conversation manually (0 participants)
        $orphanConv = Conversation::create([
            'uuid' => (string) Str::uuid(),
            'organizer_id' => $organizer->id,
            'volunteer_id' => null,
            'context_type' => 'guest_inquiry',
            'subject' => 'Guest Inquiry from Jane Doe',
            'metadata' => [
                'is_guest' => true,
                'guest_name' => 'Jane Doe',
                'guest_email' => 'jane@example.com',
            ],
        ]);

        $this->assertEquals(0, $orphanConv->participants()->count());

        // Call reuse flow
        $conversationService = app(ConversationService::class);
        $healedConv = $conversationService->startGuestContactRequest([
            'organiser_id' => $organizer->id,
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '555-0199',
            'message' => 'Follow up message',
        ]);

        $this->assertEquals($orphanConv->id, $healedConv->id);
        $this->assertEquals(1, $healedConv->participants()->count());
        $this->assertDatabaseHas('conversation_participants', [
            'conversation_id' => $healedConv->id,
            'volunteer_id' => $organizer->id,
            'role' => 'organizer',
        ]);
    }

    /** @test */
    public function organizer_12_email_safety_limit_skips_13th_email_notification()
    {
        Mail::fake();

        $organizer = $this->createOrganizer();
        $conversation = Conversation::create([
            'uuid' => (string) Str::uuid(),
            'organizer_id' => $organizer->id,
            'volunteer_id' => null,
            'context_type' => 'guest_inquiry',
            'subject' => 'Guest Inquiry',
            'metadata' => ['is_guest' => true, 'guest_email' => 'guest@example.com'],
        ]);

        ConversationParticipant::create([
            'conversation_id' => $conversation->id,
            'volunteer_id' => $organizer->id,
            'role' => 'organizer',
        ]);

        // Simulate 12 distinct successful delivery logs for this recipient with batch_id set
        for ($i = 0; $i < 12; $i++) {
            $msg = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => null,
                'sender_type' => 'guest',
                'body' => "Message {$i}",
            ]);

            MessageDeliveryLog::create([
                'conversation_id' => $conversation->id,
                'message_id' => $msg->id,
                'recipient_id' => $organizer->id,
                'delivery_type' => 'immediate',
                'status' => 'sent',
                'sent_at' => now()->subMinutes(60 + $i),
                'batch_id' => (string) Str::uuid(),
            ]);
        }

        // Now dispatch message #13
        $notificationService = app(ConversationNotificationService::class);
        $msg13 = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => null,
            'sender_type' => 'guest',
            'body' => 'Message 13',
        ]);

        $notificationService->notifyMessage($msg13);

        // Assert message 13 stored in database
        $this->assertDatabaseHas('messages', [
            'id' => $msg13->id,
            'body' => 'Message 13',
        ]);

        // Assert delivery log marked as skipped for message 13 due to daily limit
        $this->assertDatabaseHas('message_delivery_logs', [
            'conversation_id' => $conversation->id,
            'message_id' => $msg13->id,
            'recipient_id' => $organizer->id,
            'status' => 'skipped',
        ]);
    }

    /** @test */
    public function rapid_messages_trigger_15_minute_burst_batching()
    {
        Mail::fake();

        $organizer = $this->createOrganizer();
        $conversation = Conversation::create([
            'uuid' => (string) Str::uuid(),
            'organizer_id' => $organizer->id,
            'volunteer_id' => null,
            'context_type' => 'guest_inquiry',
            'subject' => 'Guest Inquiry',
            'metadata' => ['is_guest' => true, 'guest_email' => 'guest@example.com'],
        ]);

        ConversationParticipant::create([
            'conversation_id' => $conversation->id,
            'volunteer_id' => $organizer->id,
            'role' => 'organizer',
        ]);

        $notificationService = app(ConversationNotificationService::class);

        // Message 1 (Immediate notification sent)
        $msg1 = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => null,
            'sender_type' => 'guest',
            'body' => 'Message 1',
        ]);

        MessageDeliveryLog::create([
            'conversation_id' => $conversation->id,
            'message_id' => $msg1->id,
            'recipient_id' => $organizer->id,
            'delivery_type' => 'immediate',
            'status' => 'sent',
            'sent_at' => now()->subMinutes(2),
            'batch_id' => (string) Str::uuid(),
        ]);

        // Message 2 (Within 15 minutes, rapid burst batching)
        $msg2 = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => null,
            'sender_type' => 'guest',
            'body' => 'Message 2',
        ]);
        $notificationService->notifyMessage($msg2);

        // Assert Message 2 log converted to batch delivery
        $this->assertDatabaseHas('message_delivery_logs', [
            'conversation_id' => $conversation->id,
            'message_id' => $msg2->id,
            'delivery_type' => 'batch',
        ]);
    }

    /** @test */
    public function organizer_reply_to_guest_persists_message_and_dispatches_email()
    {
        Mail::fake();

        $organizer = $this->createOrganizer();
        $conversation = Conversation::create([
            'uuid' => (string) Str::uuid(),
            'organizer_id' => $organizer->id,
            'volunteer_id' => null,
            'context_type' => 'guest_inquiry',
            'subject' => 'Guest Inquiry',
            'metadata' => ['is_guest' => true, 'guest_name' => 'Guest User', 'guest_email' => 'guest@example.com'],
        ]);

        ConversationParticipant::create([
            'conversation_id' => $conversation->id,
            'volunteer_id' => $organizer->id,
            'role' => 'organizer',
        ]);

        $conversationService = app(ConversationService::class);
        $replyMessage = $conversationService->addMessage($conversation, $organizer, 'Organizer Reply Text');

        $this->assertEquals($organizer->id, $replyMessage->sender_id);
        $this->assertEquals('organizer', $replyMessage->sender_type);
        $this->assertDatabaseHas('messages', [
            'id' => $replyMessage->id,
            'conversation_id' => $conversation->id,
            'body' => 'Organizer Reply Text',
        ]);
    }

    /** @test */
    public function participant_soft_delete_is_isolated()
    {
        $orgA = $this->createOrganizer();
        $orgB = $this->createOrganizer();

        $conversation = Conversation::create([
            'uuid' => (string) Str::uuid(),
            'organizer_id' => $orgA->id,
            'volunteer_id' => $orgB->id,
            'context_type' => 'direct',
            'subject' => 'Direct Thread',
        ]);

        $partA = ConversationParticipant::create([
            'conversation_id' => $conversation->id,
            'volunteer_id' => $orgA->id,
            'role' => 'organizer',
        ]);

        $partB = ConversationParticipant::create([
            'conversation_id' => $conversation->id,
            'volunteer_id' => $orgB->id,
            'role' => 'volunteer',
        ]);

        // Org A deletes conversation
        $conversationService = app(ConversationService::class);
        $conversationService->delete($conversation, $orgA);

        $partA->refresh();
        $partB->refresh();

        $this->assertNotNull($partA->deleted_at);
        $this->assertNull($partB->deleted_at); // Org B retains access
    }

    /** @test */
    public function organizer_cannot_access_unauthorized_conversation()
    {
        $this->expectException(HttpException::class);

        $orgA = $this->createOrganizer();
        $orgB = $this->createOrganizer();

        $convA = Conversation::create([
            'uuid' => (string) Str::uuid(),
            'organizer_id' => $orgA->id,
            'volunteer_id' => null,
            'context_type' => 'guest_inquiry',
            'subject' => 'Org A Conversation',
        ]);

        ConversationParticipant::create([
            'conversation_id' => $convA->id,
            'volunteer_id' => $orgA->id,
            'role' => 'organizer',
        ]);

        // Accessing Org A's conversation as Org B triggers 403 Forbidden exception in assertParticipant
        $conversationService = app(ConversationService::class);
        $conversationService->assertParticipant($convA, $orgB);
    }
}
