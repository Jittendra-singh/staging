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

        foreach ($message->conversation->participants as $participant) {
            if ((int) $participant->volunteer_id === (int) $message->sender_id) {
                continue;
            }

            $deliveryType = $this->effectiveDeliveryType($participant);

            if ($deliveryType === self::DELIVERY_DISABLED) {
                $this->createDeliveryLog($message, $participant->volunteer, self::DELIVERY_DISABLED, 'skipped');
                continue;
            }

            if ($this->dailyLimitReached($participant->volunteer) && $deliveryType !== self::DELIVERY_DIGEST) {
                $deliveryType = self::DELIVERY_DIGEST;
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

            $this->sendDigest($recipient, $recipientLogs);
            $this->markLogs($recipientLogs, 'sent');
            $sent++;
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

        try {
            $this->mailMessageCollection($log->recipient, collect([$log->message]), $log->conversation, false);
            $log->update(['status' => 'sent', 'sent_at' => now()]);
        } catch (\Throwable $e) {
            $log->update(['status' => 'error', 'error_message' => $e->getMessage()]);
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

        try {
            $this->mailMessageCollection($recipient, $messages, $conversation, true);
            $this->markLogs($logs, 'sent');
        } catch (\Throwable $e) {
            $this->markLogs($logs, 'error', $e->getMessage());
        }
    }

    private function sendDigest(Volunteer $recipient, Collection $logs): void
    {
        if (defined('SEND_MAIL') && !SEND_MAIL) {
            return;
        }

        $messages = $logs->pluck('message')->filter();

        Mail::to($recipient->email_address)->send(new SendMail(
            'Your VolunteerCleanup message digest',
            'pages.emails.conversation-digest',
            [
                'recipient' => $recipient,
                'logs' => $logs,
                'messages' => $messages,
            ]
        ));
    }

    private function mailMessageCollection(Volunteer $recipient, Collection $messages, $conversation, bool $isBatch): void
    {
        if (defined('SEND_MAIL') && !SEND_MAIL) {
            return;
        }

        $subject = $isBatch
            ? 'New messages on VolunteerCleanup'
            : 'New message on VolunteerCleanup';

        Mail::to($recipient->email_address)->send(new SendMail(
            $subject,
            'pages.emails.conversation-message-notification',
            [
                'recipient' => $recipient,
                'conversation' => $conversation,
                'messages' => $messages,
                'is_batch' => $isBatch,
                'conversation_url' => route('messaging.conversations.show', $conversation),
            ],
            $conversation->reply_alias
        ));
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

    private function dailyLimitReached(Volunteer $volunteer): bool
    {
        $preference = $volunteer->conversationNotificationPreference;

        if (!$preference || !$preference->daily_email_limit) {
            return false;
        }

        $sentToday = MessageDeliveryLog::where('recipient_id', $volunteer->id)
            ->where('status', 'sent')
            ->where('sent_at', '>=', now()->subDay())
            ->count();

        return $sentToday >= $preference->daily_email_limit;
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
}
