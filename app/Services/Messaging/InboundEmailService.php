<?php

namespace App\Services\Messaging;

use App\Models\Conversation;
use App\Models\InboundEmailLog;
use App\Models\Message;
use App\Models\Volunteer;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InboundEmailService
{
    public function __construct(
        private ConversationService $conversations
    ) {
    }

    public function handle(Request $request): Message
    {
        $this->verifyWebhookSecret($request);

        return DB::transaction(function () use ($request) {
            $fromEmail = $this->extractEmail($request->input('from') ?: $request->input('sender') ?: $request->input('From'));
            $toEmail = $request->input('to') ?: $request->input('recipient') ?: $request->input('To') ?: '';
            $token = $request->input('token') ?: $this->extractReplyToken($toEmail);
            $fileKeys = array_keys($request->allFiles());

            $log = InboundEmailLog::create([
                'from_email' => $fromEmail,
                'to_email' => is_array($toEmail) ? implode(',', $toEmail) : $toEmail,
                'message_id_header' => $request->input('message_id') ?: $request->input('Message-Id') ?: $request->input('Message-ID'),
                'raw_payload' => Arr::except($request->all(), array_merge(['attachments'], $fileKeys)),
            ]);

            if (!$token) {
                $log->update(['status' => 'rejected', 'error_message' => 'Missing reply token.']);
                abort(422, 'Missing reply token.');
            }

            $conversation = Conversation::where('reply_token', $token)->first();

            if (!$conversation) {
                $log->update(['status' => 'rejected', 'error_message' => 'Conversation not found for reply token.']);
                abort(404, 'Conversation not found.');
            }

            $sender = $this->senderForConversation($conversation, $fromEmail);

            if (!$sender) {
                $log->update([
                    'conversation_id' => $conversation->id,
                    'status' => 'rejected',
                    'error_message' => 'Sender is not a participant.',
                ]);
                abort(403, 'Sender is not a conversation participant.');
            }

            $body = $request->input('text')
                ?: $request->input('body-plain')
                ?: $request->input('plain')
                ?: trim(strip_tags($request->input('html') ?: $request->input('body-html') ?: $request->input('body') ?: ''));

            if (!$body) {
                $log->update(['conversation_id' => $conversation->id, 'status' => 'rejected', 'error_message' => 'Empty email body.']);
                abort(422, 'Empty email body.');
            }

            $message = $this->conversations->addMessage(
                $conversation,
                $sender,
                $this->stripQuotedReply($body),
                $this->extractFiles($request),
                'email',
                [
                    'inbound_email_log_id' => $log->id,
                    'email_message_id' => $log->message_id_header,
                    'email_references' => $request->input('references') ?: $request->input('References') ?: $request->input('in_reply_to') ?: $request->input('In-Reply-To'),
                    'subject' => $request->input('subject') ?: $request->input('Subject'),
                ]
            );

            $log->update([
                'conversation_id' => $conversation->id,
                'message_id' => $message->id,
                'status' => 'stored',
            ]);

            return $message;
        });
    }

    private function verifyWebhookSecret(Request $request): void
    {
        $expected = config('services.messaging.inbound_secret');

        if (!$expected) {
            return;
        }

        $provided = $request->header('X-Messaging-Webhook-Secret')
            ?: $request->header('X-Webhook-Secret')
            ?: $request->input('webhook_secret');

        abort_unless(hash_equals($expected, (string) $provided), 403);
    }

    private function senderForConversation(Conversation $conversation, ?string $email): ?Volunteer
    {
        if (!$email) {
            return null;
        }

        return $conversation->participants()
            ->whereHas('volunteer', function ($query) use ($email) {
                $query->whereRaw('LOWER(email_address) = ?', [strtolower($email)]);
            })
            ->first()
            ?->volunteer;
    }

    private function extractReplyToken(array|string|null $recipient): ?string
    {
        $recipient = is_array($recipient) ? implode(',', $recipient) : (string) $recipient;

        if (preg_match('/reply\+([A-Za-z0-9]+)@/', $recipient, $matches)) {
            return $matches[1];
        }

        return null;
    }

    private function extractEmail(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        if (preg_match('/<([^>]+)>/', $value, $matches)) {
            return strtolower(trim($matches[1]));
        }

        return strtolower(trim($value));
    }

    private function extractFiles(Request $request): array
    {
        $files = [];

        foreach ($request->allFiles() as $file) {
            foreach (Arr::wrap($file) as $item) {
                if (is_array($item)) {
                    $files = array_merge($files, Arr::flatten($item));
                } else {
                    $files[] = $item;
                }
            }
        }

        return $files;
    }

    private function stripQuotedReply(string $body): string
    {
        $body = preg_split('/\nOn .+ wrote:\n/i', $body)[0] ?? $body;
        $body = preg_split('/\nFrom:\s.+\n/i', $body)[0] ?? $body;

        return trim(Str::limit($body, 20000, ''));
    }
}
