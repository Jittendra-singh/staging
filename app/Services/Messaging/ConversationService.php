<?php

namespace App\Services\Messaging;

use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use App\Models\MessageAttachment;
use App\Models\PendingContactMessage;
use App\Models\Volunteer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ConversationService
{
    public function __construct(
        private ConversationNotificationService $notifications
    ) {
    }

    public function startContactRequest(array $data): array
    {
        $organizer = Volunteer::findOrFail($data['organiser_id']);
        $email = strtolower(trim($data['email']));
        /** @var Volunteer|null $currentVolunteer */
        $currentVolunteer = Auth::guard('front')->user();

        if ($currentVolunteer && strtolower($currentVolunteer->email_address) === $email && !empty($currentVolunteer->email_verified_at)) {
            $conversation = $this->createOrReuseDirectConversation(
                $organizer,
                $currentVolunteer,
                'contact_organizer',
                null,
                'Contact Organizer'
            );

            $message = $this->addMessage($conversation, $currentVolunteer, $data['message'], [], 'web', [
                'contact_name' => $data['name'],
                'contact_email' => $email,
                'contact_phone' => $data['phone'] ?? null,
            ]);

            $this->syncWithFluentCrm($currentVolunteer, 'Organizer Contact');

            return [
                'status' => 'delivered',
                'conversation' => $conversation->refresh(),
                'message' => $message,
            ];
        }

        $volunteer = $this->resolveOrCreatePendingVolunteer($data['name'], $email, $data['phone'] ?? '');
        $token = Str::random(64);

        $pending = PendingContactMessage::create([
            'organizer_id' => $organizer->id,
            'volunteer_id' => $volunteer->id,
            'token_hash' => hash('sha256', $token),
            'name' => $data['name'],
            'email' => $email,
            'phone' => $data['phone'] ?? null,
            'message' => $data['message'],
            'expires_at' => now()->addHours(24),
        ]);

        $this->notifications->sendPendingContactVerification($pending, $token);

        return [
            'status' => 'verification_sent',
            'pending' => $pending,
        ];
    }

    public function startGuestContactRequest(array $data, ?int $organiserLeadId = null): Conversation
    {
        $organizer = Volunteer::findOrFail($data['organiser_id']);
        $email = strtolower(trim($data['email']));

        $conversation = Conversation::where('organizer_id', $organizer->id)
            ->whereNull('volunteer_id')
            ->where('context_type', 'guest_inquiry')
            ->where('status', 'active')
            ->where('metadata->guest_email', $email)
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'organizer_id' => $organizer->id,
                'volunteer_id' => null,
                'context_type' => 'guest_inquiry',
                'context_id' => $organiserLeadId,
                'subject' => 'Guest Inquiry from ' . $data['name'],
                'status' => 'active',
                'metadata' => [
                    'is_guest' => true,
                    'guest_name' => $data['name'],
                    'guest_email' => $email,
                    'guest_phone' => $data['phone'] ?? null,
                ],
            ]);
        } else {
            $conversation->update([
                'metadata' => array_merge($conversation->metadata ?? [], [
                    'is_guest' => true,
                    'guest_name' => $data['name'],
                    'guest_email' => $email,
                    'guest_phone' => $data['phone'] ?? ($conversation->metadata['guest_phone'] ?? null),
                ]),
            ]);
        }

        ConversationParticipant::firstOrCreate([
            'conversation_id' => $conversation->id,
            'volunteer_id'    => $organizer->id,
        ], [
            'role' => 'organizer',
        ]);

        ConversationParticipant::where('conversation_id', $conversation->id)
            ->where('volunteer_id', $organizer->id)
            ->update([
                'archived_at' => null,
                'deleted_at'  => null,
            ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => null,
            'sender_type' => 'guest',
            'body' => $data['message'],
            'source' => 'web',
            'metadata' => [
                'guest_name' => $data['name'],
                'guest_email' => $email,
                'guest_phone' => $data['phone'] ?? null,
            ],
        ]);

        $conversation->update([
            'latest_message_id' => $message->id,
            'last_message_at' => now(),
        ]);

        if (!defined('SEND_MAIL') || SEND_MAIL) {
            \Illuminate\Support\Facades\Mail::to($organizer->email_address)->send(new \App\Mail\SendMail(
                'New Guest Inquiry from ' . $data['name'],
                'pages.emails.contact-organizer',
                [
                    'org_name'    => trim($organizer->first_name . ' ' . $organizer->last_name),
                    'c_volunteer' => $data['name'],
                    'c_email'     => $email,
                    'c_phone'     => $data['phone'] ?: 'N/A',
                    'c_message'   => $data['message'],
                ],
                $email
            ));
        }

        return $conversation;
    }

    public function verifyPendingContact(string $token): Conversation
    {
        $volunteerToSync = null;

        $conversation = DB::transaction(function () use ($token, &$volunteerToSync) {
            $pending = PendingContactMessage::where('token_hash', hash('sha256', $token))
                ->whereNull('consumed_at')
                ->where('expires_at', '>=', now())
                ->lockForUpdate()
                ->firstOrFail();

            $volunteer = $pending->volunteer ?: $this->resolveOrCreatePendingVolunteer($pending->name, $pending->email, $pending->phone ?? '');

            if (empty($volunteer->email_verified_at)) {
                $volunteer->email_verified_at = now();
                $volunteer->save();
            }

            $conversation = $this->createOrReuseDirectConversation(
                $pending->organizer,
                $volunteer,
                'contact_organizer',
                null,
                'Contact Organizer'
            );

            $message = $this->addMessage($conversation, $volunteer, $pending->message, [], 'web', [
                'contact_name' => $pending->name,
                'contact_email' => $pending->email,
                'contact_phone' => $pending->phone,
                'pending_contact_message_id' => $pending->id,
            ]);

            $pending->conversation_id = $conversation->id;
            $pending->consumed_at = now();
            $pending->save();

            Auth::guard('front')->login($volunteer);
            $volunteerToSync = $volunteer;

            return $conversation->refresh();
        });

        if ($volunteerToSync) {
            $this->syncWithFluentCrm($volunteerToSync, 'Organizer Contact Verification');
        }

        return $conversation;
    }

    public function syncWithFluentCrm(Volunteer $volunteer, string $tag = ''): void
    {
        try {
            $url = config('app.wp_sync_url');
            if (empty($url)) {
                return;
            }

            $role = $volunteer->is_organiser ? 'Organizer' : 'Volunteer';
            $data = [
                'first_name' => $volunteer->first_name,
                'last_name' => $volunteer->last_name,
                'full_name' => trim($volunteer->first_name . ' ' . $volunteer->last_name),
                'email' => $volunteer->email_address,
                'phone' => $volunteer->phone ?: '',
                'address_line_1' => $volunteer->address_line_1 ?: '',
                'postal_code' => $volunteer->pincode ?: '',
                'country' => 'United States',
                'tags' => array_values(array_filter(['Newsletter', $role, $tag])),
                'fcrm_vol_newsletter' => 'Yes',
            ];

            Http::timeout(5)->post($url, $data);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('FluentCRM sync failed: ' . $e->getMessage());
        }
    }

    public function createOrReuseDirectConversation(
        Volunteer $organizer,
        Volunteer $volunteer,
        string $contextType = 'contact_organizer',
        ?int $contextId = null,
        ?string $subject = null
    ): Conversation {
        $conversation = Conversation::where('organizer_id', $organizer->id)
            ->where('volunteer_id', $volunteer->id)
            ->where('context_type', $contextType)
            ->when($contextId, fn ($query) => $query->where('context_id', $contextId))
            ->where('status', 'active')
            ->first();

        if ($conversation) {
            $this->ensureParticipants($conversation, $organizer, $volunteer);
            return $this->ensureReplyAlias($conversation);
        }

        $conversation = Conversation::create([
            'organizer_id' => $organizer->id,
            'volunteer_id' => $volunteer->id,
            'context_type' => $contextType,
            'context_id' => $contextId,
            'subject' => $subject ?: 'Conversation',
            'status' => 'active',
        ]);

        $this->ensureParticipants($conversation, $organizer, $volunteer);

        return $this->ensureReplyAlias($conversation);
    }

    public function addMessage(
        Conversation $conversation,
        Volunteer $sender,
        string $body,
        array $attachments = [],
        string $source = 'web',
        array $metadata = []
    ): Message {
        $this->assertParticipant($conversation, $sender);

        return DB::transaction(function () use ($conversation, $sender, $body, $attachments, $source, $metadata) {
            $conversation->participants()->update(['archived_at' => null, 'deleted_at' => null]);

            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $sender->id,
                'sender_type' => $sender->is_organiser ? 'organizer' : 'volunteer',
                'body' => trim($body),
                'source' => $source,
                'email_message_id' => $metadata['email_message_id'] ?? null,
                'email_references' => $metadata['email_references'] ?? null,
                'inbound_email_log_id' => $metadata['inbound_email_log_id'] ?? null,
                'metadata' => $metadata,
            ]);

            $this->storeAttachments($message, $attachments);

            $conversation->latest_message_id = $message->id;
            $conversation->last_message_at = now();
            $conversation->status = 'active';
            $conversation->save();

            $this->markRead($conversation, $sender, $message);
            $this->notifications->notifyMessage($message);

            return $message->load(['sender', 'attachments']);
        });
    }

    public function markRead(Conversation $conversation, Volunteer $volunteer, ?Message $message = null): void
    {
        $message = $message ?: $conversation->messages()->latest('id')->first();

        $conversation->participants()
            ->where('volunteer_id', $volunteer->id)
            ->update([
                'last_read_message_id' => $message?->id,
                'last_read_at' => now(),
            ]);
    }

    public function markUnread(Conversation $conversation, Volunteer $volunteer): void
    {
        $this->assertParticipant($conversation, $volunteer);

        $conversation->participants()
            ->where('volunteer_id', $volunteer->id)
            ->update([
                'last_read_message_id' => null,
                'last_read_at' => null,
            ]);
    }

    public function bulkMarkRead(array $uuids, Volunteer $volunteer): int
    {
        $conversations = Conversation::forParticipant($volunteer)
            ->whereIn('uuid', $uuids)
            ->with(['latestMessage'])
            ->get();

        $count = 0;
        foreach ($conversations as $conversation) {
            $latestId = $conversation->latest_message_id ?: $conversation->messages()->latest('id')->value('id');
            $conversation->participants()
                ->where('volunteer_id', $volunteer->id)
                ->update([
                    'last_read_message_id' => $latestId,
                    'last_read_at' => now(),
                ]);
            $count++;
        }

        return $count;
    }

    public function bulkMarkUnread(array $uuids, Volunteer $volunteer): int
    {
        $conversations = Conversation::forParticipant($volunteer)
            ->whereIn('uuid', $uuids)
            ->get();

        $count = 0;
        foreach ($conversations as $conversation) {
            $conversation->participants()
                ->where('volunteer_id', $volunteer->id)
                ->update([
                    'last_read_message_id' => null,
                    'last_read_at' => null,
                ]);
            $count++;
        }

        return $count;
    }

    public function bulkArchive(array $uuids, Volunteer $volunteer): int
    {
        $conversations = Conversation::forParticipant($volunteer)
            ->whereIn('uuid', $uuids)
            ->get();

        $count = 0;
        foreach ($conversations as $conversation) {
            $conversation->participants()
                ->where('volunteer_id', $volunteer->id)
                ->update(['archived_at' => now()]);
            $count++;
        }

        return $count;
    }

    public function bulkUnarchive(array $uuids, Volunteer $volunteer): int
    {
        $conversations = Conversation::forParticipant($volunteer)
            ->whereIn('uuid', $uuids)
            ->get();

        $count = 0;
        foreach ($conversations as $conversation) {
            $conversation->participants()
                ->where('volunteer_id', $volunteer->id)
                ->update(['archived_at' => null]);
            $count++;
        }

        return $count;
    }

    public function delete(Conversation $conversation, Volunteer $volunteer): void
    {
        $this->assertParticipant($conversation, $volunteer);

        $conversation->participants()
            ->where('volunteer_id', $volunteer->id)
            ->update(['deleted_at' => now()]);
    }

    public function bulkDelete(array $uuids, Volunteer $volunteer): int
    {
        $conversations = Conversation::forParticipant($volunteer)
            ->whereIn('uuid', $uuids)
            ->get();

        $count = 0;
        foreach ($conversations as $conversation) {
            $conversation->participants()
                ->where('volunteer_id', $volunteer->id)
                ->update(['deleted_at' => now()]);
            $count++;
        }

        return $count;
    }

    public function archive(Conversation $conversation, Volunteer $volunteer): void
    {
        $this->assertParticipant($conversation, $volunteer);

        $conversation->participants()
            ->where('volunteer_id', $volunteer->id)
            ->update(['archived_at' => now()]);
    }

    public function unarchive(Conversation $conversation, Volunteer $volunteer): void
    {
        $this->assertParticipant($conversation, $volunteer);

        $conversation->participants()
            ->where('volunteer_id', $volunteer->id)
            ->update(['archived_at' => null]);
    }

    public function updateThreadPreference(Conversation $conversation, Volunteer $volunteer, ?string $preference): void
    {
        $this->assertParticipant($conversation, $volunteer);

        $conversation->participants()
            ->where('volunteer_id', $volunteer->id)
            ->update(['notification_preference' => $preference]);
    }

    public function updateGlobalPreference(Volunteer $volunteer, array $data): void
    {
        $volunteer->conversationNotificationPreference()->updateOrCreate(
            ['volunteer_id' => $volunteer->id],
            [
                'default_delivery' => $data['default_delivery'],
                'daily_email_limit' => $data['daily_email_limit'],
                'digest_hour' => $data['digest_hour'],
                'muted_until' => $data['muted_until'] ?? null,
            ]
        );
    }

    public function assertParticipant(Conversation $conversation, Volunteer $volunteer): void
    {
        $exists = $conversation->participants()
            ->where('volunteer_id', $volunteer->id)
            ->exists();

        abort_unless($exists, 403);
    }

    private function resolveOrCreatePendingVolunteer(string $name, string $email, string $phone = ''): Volunteer
    {
        $volunteer = Volunteer::where('email_address', $email)->first();

        if ($volunteer) {
            if ($volunteer->status != 1) {
                throw ValidationException::withMessages([
                    'email' => 'This account is not active. Please contact support.',
                ]);
            }

            return $volunteer;
        }

        $parts = preg_split('/\s+/', trim($name), 2);

        $volunteer = new Volunteer();
        $volunteer->first_name = $parts[0] ?: 'Volunteer';
        $volunteer->last_name = $parts[1] ?? '';
        $volunteer->email_address = $email;
        $volunteer->password = Hash::make(Str::random(32));
        $volunteer->is_organiser = 0;
        $volunteer->is_newsletter = 0;
        $volunteer->status = 1;
        $volunteer->phone = $phone;
        $volunteer->address_line_1 = '';
        $volunteer->address_line_2 = '';
        $volunteer->city_id = 0;
        $volunteer->pincode = '';
        $volunteer->save();

        return $volunteer;
    }

    private function ensureParticipants(Conversation $conversation, Volunteer $organizer, Volunteer $volunteer): void
    {
        $conversation->participants()->updateOrCreate(
            ['volunteer_id' => $organizer->id],
            ['role' => 'organizer']
        );

        $conversation->participants()->updateOrCreate(
            ['volunteer_id' => $volunteer->id],
            ['role' => 'volunteer']
        );
    }

    private function ensureReplyAlias(Conversation $conversation): Conversation
    {
        if ($conversation->reply_alias) {
            return $conversation;
        }

        $domain = config('services.messaging.reply_domain');

        if (!$domain) {
            $from = config('mail.from.address');
            $domain = $from && str_contains($from, '@') ? Str::after($from, '@') : parse_url(config('app.url'), PHP_URL_HOST);
        }

        $conversation->reply_alias = 'reply+' . $conversation->reply_token . '@' . $domain;
        $conversation->save();

        return $conversation;
    }

    private function storeAttachments(Message $message, array $attachments): void
    {
        foreach ($attachments as $attachment) {
            if (!$attachment instanceof UploadedFile || !$attachment->isValid()) {
                continue;
            }

            $directory = public_path('messaging/attachments');

            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            $filename = now()->format('YmdHis') . '_' . Str::random(12) . '.' . $attachment->getClientOriginalExtension();
            $attachment->move($directory, $filename);

            MessageAttachment::create([
                'message_id' => $message->id,
                'path' => 'messaging/attachments/' . $filename,
                'original_name' => $attachment->getClientOriginalName(),
                'mime_type' => $attachment->getClientMimeType(),
                'size' => $attachment->getSize() ?: 0,
            ]);
        }
    }
}
