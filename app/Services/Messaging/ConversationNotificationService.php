<?php

namespace App\Services\Messaging;

use App\Jobs\Messaging\SendConversationDigestNotifications;
use App\Jobs\Messaging\SendConversationMessageNotification;
use App\Mail\SendMail;
use App\Models\ConversationNotificationPreference;
use App\Models\ConversationParticipant;
use App\Models\Message;
use App\Models\MessageDeliveryLog;
use App\Models\PendingContactMessage;
use App\Models\Volunteer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

class ConversationNotificationService
{
    public const DELIVERY_IMMEDIATE = 'immediate';
    public const DELIVERY_BATCH = 'batch';
    public const DELIVERY_DIGEST = 'digest';
    public const DELIVERY_DISABLED = 'disabled';

    public function notifyMessage(Message $message): void
    {
        $message->loadMissing('conversation.participants.volunteer', 'sender');
        $conversation = $message->conversation;

        $isGuest = empty($conversation->volunteer_id) || !empty($conversation->metadata['is_guest']);

        if ($isGuest && (int) $message->sender_id === (int) $conversation->organizer_id) {
            $guestEmail = $conversation->metadata['guest_email'] ?? null;
            $guestName = $conversation->metadata['guest_name'] ?? 'Guest';

            if ($guestEmail && (!defined('SEND_MAIL') || SEND_MAIL)) {
                $organizer = $message->sender ?: Volunteer::find($conversation->organizer_id);
                $orgName = $organizer ? trim($organizer->first_name . ' ' . $organizer->last_name) : 'Organizer';

                try {
                    Mail::to($guestEmail)->send(new SendMail(
                        'Reply from ' . $orgName,
                        'pages.emails.guest-message-reply',
                        [
                            'guest_name'     => $guestName,
                            'organizer_name' => $orgName,
                            'reply_message'  => $message->body,
                            'subject'        => $conversation->subject,
                        ],
                        $organizer ? $organizer->email_address : null
                    ));
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to send guest reply email: ' . $e->getMessage());
                }
            }
            return;
        }

        foreach ($message->conversation->participants as $participant) {
            if ((int) $participant->volunteer_id === (int) $message->sender_id) {
                continue;
            }

            $deliveryType = $this->effectiveDeliveryType($participant);

            if ($deliveryType === self::DELIVERY_DISABLED) {
                $this->createDeliveryLog($message, $participant->volunteer, self::DELIVERY_DISABLED, 'skipped');
                continue;
            }

            if ($this->dailyLimitReached($participant->volunteer, $message->conversation_id)) {
                $this->createDeliveryLog($message, $participant->volunteer, self::DELIVERY_DISABLED, 'skipped');
                continue;
            }

            if ($deliveryType === self::DELIVERY_IMMEDIATE) {
                $lastReadAt = $participant->last_read_at;

                $burstActive = MessageDeliveryLog::where('conversation_id', $message->conversation_id)
                    ->where('recipient_id', $participant->volunteer_id)
                    ->where(function ($query) use ($lastReadAt) {
                        $query->where(function ($q) use ($lastReadAt) {
                            $q->where('status', 'sent')
                              ->where('sent_at', '>=', now()->subMinutes(15))
                              ->when($lastReadAt, fn ($sq) => $sq->where('sent_at', '>', $lastReadAt));
                        })->orWhere(function ($q) use ($lastReadAt) {
                            $q->where('delivery_type', self::DELIVERY_BATCH)
                              ->where('status', 'pending')
                              ->when($lastReadAt, fn ($sq) => $sq->where('created_at', '>', $lastReadAt));
                        });
                    })
                    ->exists();

                if ($burstActive) {
                    $deliveryType = self::DELIVERY_BATCH;
                }
            }

            $log = $this->createDeliveryLog($message, $participant->volunteer, $deliveryType);

            if ($deliveryType === self::DELIVERY_IMMEDIATE) {
                SendConversationMessageNotification::dispatch($log->id)->afterCommit();
            } elseif ($deliveryType === self::DELIVERY_BATCH) {
                SendConversationMessageNotification::dispatch($log->id)->delay(now()->addMinutes(15))->afterCommit();
            }
        }
    }

    public function sendPendingContactVerification(PendingContactMessage $pending, string $token): void
    {
        if (defined('SEND_MAIL') && !SEND_MAIL) {
            return;
        }

        Mail::to($pending->email)->send(new SendMail(
            'Verify your message to the organizer',
            'pages.emails.conversation-contact-verification',
            [
                'name' => $pending->name,
                'organizer' => $pending->organizer,
                'verification_link' => route('messaging.pending.verify', ['token' => $token]),
                'expires_at' => $pending->expires_at,
            ]
        ));
    }

    public function sendDeliveryLog(MessageDeliveryLog $log): void
    {
        if ($log->status !== 'pending') {
            return;
        }

        if ($log->delivery_type === self::DELIVERY_BATCH) {
            $this->sendBatch($log);
            return;
        }

        $this->sendSingleMessage($log);
    }

    public function dispatchDigestJob(): void
    {
        SendConversationDigestNotifications::dispatch();
    }

    public function sendPendingDigests(): int
    {
        $logs = MessageDeliveryLog::with(['message.sender', 'conversation', 'recipient'])
            ->where('delivery_type', self::DELIVERY_DIGEST)
            ->where('status', 'pending')
            ->get()
            ->groupBy('recipient_id');

        $sent = 0;

        foreach ($logs as $recipientLogs) {
            $recipient = $recipientLogs->first()->recipient;

            if (!$recipient) {
                $this->markLogs($recipientLogs, 'skipped');
                continue;
            }

            $conversationGroups = $recipientLogs->groupBy('conversation_id');

            foreach ($conversationGroups as $conversationId => $convLogs) {
                $logIds = $convLogs->pluck('id')->toArray();
                $batchId = $this->reserveTransmissionSlot((int) $conversationId, $recipient, $logIds);

                if (!$batchId) {
                    continue;
                }

                try {
                    $this->sendDigest($recipient, $convLogs);
                    MessageDeliveryLog::where('batch_id', $batchId)->update(['status' => 'sent', 'sent_at' => now()]);
                    $sent++;
                } catch (\Throwable $e) {
                    MessageDeliveryLog::where('batch_id', $batchId)->update(['status' => 'error', 'error_message' => $e->getMessage()]);
                }
            }
        }

        return $sent;
    }

    private function sendSingleMessage(MessageDeliveryLog $log): void
    {
        $log->loadMissing(['message.sender', 'message.conversation', 'recipient', 'conversation']);

        if (!$log->recipient || !$log->message) {
            $log->update(['status' => 'skipped']);
            return;
        }

        $batchId = $this->reserveTransmissionSlot($log->conversation_id, $log->recipient, [$log->id]);

        if (!$batchId) {
            return;
        }

        try {
            $this->mailMessageCollection($log->recipient, collect([$log->message]), $log->conversation, false);
            MessageDeliveryLog::where('batch_id', $batchId)->update(['status' => 'sent', 'sent_at' => now()]);
        } catch (\Throwable $e) {
            MessageDeliveryLog::where('batch_id', $batchId)->update(['status' => 'error', 'error_message' => $e->getMessage()]);
        }
    }

    private function sendBatch(MessageDeliveryLog $seedLog): void
    {
        $logs = MessageDeliveryLog::with(['message.sender', 'recipient', 'conversation'])
            ->where('recipient_id', $seedLog->recipient_id)
            ->where('conversation_id', $seedLog->conversation_id)
            ->where('delivery_type', self::DELIVERY_BATCH)
            ->where('status', 'pending')
            ->where('created_at', '<=', now())
            ->get();

        if ($logs->isEmpty()) {
            return;
        }

        $recipient = $logs->first()->recipient;
        $conversation = $logs->first()->conversation;
        $messages = $logs->pluck('message')->filter();

        if (!$recipient || !$conversation || $messages->isEmpty()) {
            $this->markLogs($logs, 'skipped');
            return;
        }

        $logIds = $logs->pluck('id')->toArray();
        $batchId = $this->reserveTransmissionSlot($conversation->id, $recipient, $logIds);

        if (!$batchId) {
            return;
        }

        try {
            $this->mailMessageCollection($recipient, $messages, $conversation, true);
            MessageDeliveryLog::where('batch_id', $batchId)->update(['status' => 'sent', 'sent_at' => now()]);
        } catch (\Throwable $e) {
            MessageDeliveryLog::where('batch_id', $batchId)->update(['status' => 'error', 'error_message' => $e->getMessage()]);
        }
    }

    private function sendDigest(Volunteer $recipient, Collection $logs): void
    {
        $logs->loadMissing(['message.sender', 'recipient', 'conversation']);
        $conversation = $logs->first()?->conversation;
        $messages = $logs->pluck('message')->filter();

        if (!$conversation || $messages->isEmpty()) {
            return;
        }

        $this->mailMessageCollection($recipient, $messages, $conversation, true);
    }

    private function effectiveDeliveryType(ConversationParticipant $participant): string
    {
        if ($participant->muted_until && $participant->muted_until->isFuture()) {
            return self::DELIVERY_DISABLED;
        }

        if ($participant->notification_preference) {
            return $participant->notification_preference;
        }

        $preference = ConversationNotificationPreference::firstOrCreate(
            ['volunteer_id' => $participant->volunteer_id],
            ['default_delivery' => self::DELIVERY_IMMEDIATE]
        );

        if ($preference->muted_until && $preference->muted_until->isFuture()) {
            return self::DELIVERY_DISABLED;
        }

        return $preference->default_delivery ?: self::DELIVERY_IMMEDIATE;
    }

    private function reserveTransmissionSlot(int $conversationId, Volunteer $recipient, array $logIds): ?string
    {
        if (empty($logIds)) {
            return null;
        }

        $batchId = (string) \Illuminate\Support\Str::uuid();

        return \Illuminate\Support\Facades\DB::transaction(function () use ($conversationId, $recipient, $logIds, $batchId) {
            $participant = ConversationParticipant::where('conversation_id', $conversationId)
                ->where('volunteer_id', $recipient->id)
                ->lockForUpdate()
                ->first();

            if (!$participant) {
                return null;
            }

            $preference = $recipient->conversationNotificationPreference;
            $limit = ($preference && $preference->daily_email_limit) ? (int) $preference->daily_email_limit : 12;
            $limit = min($limit, 12);

            $sentWithBatchId = MessageDeliveryLog::where('conversation_id', $conversationId)
                ->where('recipient_id', $recipient->id)
                ->where('status', 'sent')
                ->where('sent_at', '>=', now()->subDay())
                ->whereNotNull('batch_id')
                ->distinct()
                ->count('batch_id');

            $sentLegacy = MessageDeliveryLog::where('conversation_id', $conversationId)
                ->where('recipient_id', $recipient->id)
                ->where('status', 'sent')
                ->where('sent_at', '>=', now()->subDay())
                ->whereNull('batch_id')
                ->distinct()
                ->count('sent_at');

            $processingReservations = MessageDeliveryLog::where('conversation_id', $conversationId)
                ->where('recipient_id', $recipient->id)
                ->where('status', 'processing')
                ->where('updated_at', '>=', now()->subMinutes(10))
                ->whereNotNull('batch_id')
                ->distinct()
                ->count('batch_id');

            $totalTransmissions = $sentWithBatchId + $sentLegacy + $processingReservations;

            if ($totalTransmissions >= $limit) {
                MessageDeliveryLog::whereIn('id', $logIds)
                    ->where('status', 'pending')
                    ->update([
                        'status' => 'skipped',
                        'error_message' => 'Daily email safety limit reached',
                    ]);

                return null;
            }

            $updated = MessageDeliveryLog::whereIn('id', $logIds)
                ->where('status', 'pending')
                ->update([
                    'status' => 'processing',
                    'batch_id' => $batchId,
                ]);

            if ($updated === 0) {
                return null;
            }

            return $batchId;
        });
    }

    private function dailyLimitReached(Volunteer $volunteer, int $conversationId): bool
    {
        $preference = $volunteer->conversationNotificationPreference;

        $limit = ($preference && $preference->daily_email_limit) ? (int) $preference->daily_email_limit : 12;
        $limit = min($limit, 12);

        $sentWithBatchId = MessageDeliveryLog::where('conversation_id', $conversationId)
            ->where('recipient_id', $volunteer->id)
            ->where('status', 'sent')
            ->where('sent_at', '>=', now()->subDay())
            ->whereNotNull('batch_id')
            ->distinct()
            ->count('batch_id');

        $sentLegacy = MessageDeliveryLog::where('conversation_id', $conversationId)
            ->where('recipient_id', $volunteer->id)
            ->where('status', 'sent')
            ->where('sent_at', '>=', now()->subDay())
            ->whereNull('batch_id')
            ->distinct()
            ->count('sent_at');

        $processingReservations = MessageDeliveryLog::where('conversation_id', $conversationId)
            ->where('recipient_id', $volunteer->id)
            ->where('status', 'processing')
            ->where('updated_at', '>=', now()->subMinutes(10))
            ->whereNotNull('batch_id')
            ->distinct()
            ->count('batch_id');

        return ($sentWithBatchId + $sentLegacy + $processingReservations) >= $limit;
    }

    private function createDeliveryLog(Message $message, Volunteer $recipient, string $deliveryType, string $status = 'pending'): MessageDeliveryLog
    {
        return MessageDeliveryLog::create([
            'conversation_id' => $message->conversation_id,
            'message_id' => $message->id,
            'recipient_id' => $recipient->id,
            'delivery_type' => $deliveryType,
            'status' => $status,
        ]);
    }
    private function markLogs(Collection $logs, string $status, ?string $error = null): void
    {
        foreach ($logs as $log) {
            $log->update([
                'status' => $status,
                'sent_at' => $status === 'sent' ? now() : $log->sent_at,
                'error_message' => $error,
            ]);
        }
    }

    private function mailMessageCollection(
        Volunteer $recipient,
        Collection $messages,
        \App\Models\Conversation $conversation,
        bool $isDigest
    ): void {
        if (defined('SEND_MAIL') && !SEND_MAIL) {
            throw new \RuntimeException('Email sending is disabled by SEND_MAIL.');
        }

        $messages = $messages->filter()->values();

        if ($messages->isEmpty()) {
            throw new \InvalidArgumentException(
                'No messages provided for notification.'
            );
        }

        $recipientName = trim(
            ($recipient->first_name ?? '') . ' ' .
            ($recipient->last_name ?? '')
        );

        Mail::to($recipient->email_address)->send(new SendMail(
            $isDigest
                ? 'New messages in your VolunteerCleanup conversation'
                : 'New message in your VolunteerCleanup conversation',
            'pages.emails.conversation-message-notification',
            [
                'recipient_name' => $recipientName ?: 'there',
                'conversation_subject' => $conversation->subject ?: 'Conversation',
                'messages' => $messages,
                'is_digest' => $isDigest,
            ]
        ));
    }
}