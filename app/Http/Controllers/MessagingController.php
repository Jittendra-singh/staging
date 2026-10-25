<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Services\Messaging\ConversationNotificationService;
use App\Services\Messaging\ConversationService;
use Illuminate\Http\Request;

class MessagingController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\Volunteer $volunteer */
        $volunteer = auth()->guard('front')->user();
        $tab = $request->get('tab', $request->boolean('archived') ? 'archived' : 'all');
        $status = $request->get('status', 'all');
        $search = trim((string) $request->get('search'));

        // Real counts for tabs & badges (excluding deleted_at for current participant)
        $baseParticipant = Conversation::forParticipant($volunteer);

        $allCount = (clone $baseParticipant)->whereHas('participants', function ($q) use ($volunteer) {
            $q->where('volunteer_id', $volunteer->id)
              ->whereNull('archived_at')
              ->whereNull('deleted_at');
        })->count();

        $archivedCount = (clone $baseParticipant)->whereHas('participants', function ($q) use ($volunteer) {
            $q->where('volunteer_id', $volunteer->id)
              ->whereNotNull('archived_at')
              ->whereNull('deleted_at');
        })->count();

        $unreadCount = (clone $baseParticipant)->whereHas('participants', function ($q) use ($volunteer) {
            $q->where('volunteer_id', $volunteer->id)
              ->whereNull('archived_at')
              ->whereNull('deleted_at');
        })->get()->filter(fn ($conv) => $conv->unreadCountFor($volunteer) > 0)->count();

        // Main conversations query
        $query = Conversation::forParticipant($volunteer)
            ->with(['latestMessage.sender', 'participants.volunteer']);

        if ($tab === 'archived' || $status === 'archived') {
            $query->whereHas('participants', function ($q) use ($volunteer) {
                $q->where('volunteer_id', $volunteer->id)
                  ->whereNotNull('archived_at')
                  ->whereNull('deleted_at');
            });
        } else {
            $query->whereHas('participants', function ($q) use ($volunteer) {
                $q->where('volunteer_id', $volunteer->id)
                  ->whereNull('archived_at')
                  ->whereNull('deleted_at');
            });
        }

        if ($status === 'unread') {
            $query->whereHas('messages', function ($mQ) use ($volunteer) {
                $mQ->where('sender_id', '!=', $volunteer->id);
            })->whereHas('participants', function ($pQ) use ($volunteer) {
                $pQ->where('volunteer_id', $volunteer->id)
                   ->whereNull('deleted_at')
                   ->where(function ($sq) {
                       $sq->whereNull('last_read_message_id')
                          ->orWhereColumn('last_read_message_id', '<', 'conversations.latest_message_id');
                   });
            });
        } elseif ($status === 'read') {
            $query->whereHas('participants', function ($pQ) use ($volunteer) {
                $pQ->where('volunteer_id', $volunteer->id)
                   ->whereNull('deleted_at')
                   ->whereNotNull('last_read_message_id')
                   ->whereColumn('last_read_message_id', '>=', 'conversations.latest_message_id');
            });
        }

        if ($search) {
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('subject', 'like', "%{$search}%")
                    ->orWhere('metadata->guest_name', 'like', "%{$search}%")
                    ->orWhere('metadata->guest_email', 'like', "%{$search}%")
                    ->orWhereHas('messages', fn ($messageQuery) => $messageQuery->where('body', 'like', "%{$search}%"))
                    ->orWhereHas('participants.volunteer', function ($volunteerQuery) use ($search) {
                        $volunteerQuery->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('email_address', 'like', "%{$search}%");
                    });
            });
        }

        $conversations = $query->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $preference = $volunteer->conversationNotificationPreference()->firstOrCreate(
            ['volunteer_id' => $volunteer->id],
            ['default_delivery' => ConversationNotificationService::DELIVERY_IMMEDIATE]
        );

        return view('pages.frontend.messaging.index', [
            'conversations' => $conversations,
            'preference' => $preference,
            'showArchived' => $tab === 'archived' || $status === 'archived',
            'search' => $search,
            'status' => $status,
            'tab' => $tab,
            'allCount' => $allCount,
            'archivedCount' => $archivedCount,
            'unreadCount' => $unreadCount,
            'seo_details' => (object) ['title' => 'Inbox'],
        ]);
    }

    public function show(Conversation $conversation, ConversationService $conversations)
    {
        /** @var \App\Models\Volunteer $volunteer */
        $volunteer = auth()->guard('front')->user();
        $conversations->assertParticipant($conversation, $volunteer);

        $conversation->load([
            'participants.volunteer',
            'messages' => fn ($query) => $query->with(['sender', 'attachments'])->oldest('id'),
        ]);

        $conversations->markRead($conversation, $volunteer);

        return view('pages.frontend.messaging.show', [
            'conversation' => $conversation,
            'participant' => $conversation->participantFor($volunteer),
            'seo_details' => (object) ['title' => 'Conversation'],
        ]);
    }

    public function delta(Request $request, Conversation $conversation, ConversationService $conversations)
    {
        /** @var \App\Models\Volunteer $volunteer */
        $volunteer = auth()->guard('front')->user();
        $conversations->assertParticipant($conversation, $volunteer);

        $afterId = (int) $request->get('after_id', 0);

        $newMessages = $conversation->messages()
            ->where('id', '>', $afterId)
            ->with(['sender', 'attachments'])
            ->oldest('id')
            ->get();

        if ($newMessages->isNotEmpty()) {
            $conversations->markRead($conversation, $volunteer, $newMessages->last());
        }

        $formattedMessages = $newMessages->map(function ($message) use ($volunteer) {
            $senderName = 'System';
            if ($message->sender) {
                $senderName = trim($message->sender->first_name . ' ' . $message->sender->last_name);
            } elseif (!empty($message->metadata['guest_name'])) {
                $senderName = $message->metadata['guest_name'];
            }

            return [
                'id' => $message->id,
                'sender_id' => $message->sender_id,
                'sender_name' => $senderName,
                'body' => $message->body ?: '',
                'created_at' => $message->created_at ? $message->created_at->format('M d, Y h:i A') : '',
                'is_mine' => (int) $message->sender_id === (int) $volunteer->id,
                'attachments' => $message->attachments->map(function ($attachment) {
                    return [
                        'id' => $attachment->id,
                        'original_name' => $attachment->original_name,
                        'url' => $attachment->url,
                    ];
                })->values(),
            ];
        })->values();

        return response()->json([
            'status' => true,
            'messages' => $formattedMessages,
        ]);
    }

    public function store(Request $request, Conversation $conversation, ConversationService $conversations)
    {
        $validated = $request->validate([
            'message' => 'nullable|string|max:2000',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|max:10240|mimes:jpg,jpeg,png,gif,pdf',
        ]);

        $files = $request->file('attachments', []);

        if (empty(trim((string) ($validated['message'] ?? ''))) && empty($files)) {
            return redirect()->back()->withErrors(['message' => 'Please enter a message or attach a file.']);
        }

        /** @var \App\Models\Volunteer $user */
        $user = auth()->guard('front')->user();

        $conversations->addMessage(
            $conversation,
            $user,
            $validated['message'] ?? '',
            $files
        );

        $this->setFlashSession(true, 'Message sent successfully.');
        return redirect()->route('messaging.conversations.show', $conversation);
    }

    public function archive(Conversation $conversation, ConversationService $conversations)
    {
        /** @var \App\Models\Volunteer $user */
        $user = auth()->guard('front')->user();
        $conversations->archive($conversation, $user);

        $this->setFlashSession(true, 'Conversation archived.');
        return redirect()->route('contact-lists');
    }

    public function unarchive(Conversation $conversation, ConversationService $conversations)
    {
        /** @var \App\Models\Volunteer $user */
        $user = auth()->guard('front')->user();
        $conversations->unarchive($conversation, $user);

        $this->setFlashSession(true, 'Conversation restored.');
        return redirect()->route('messaging.conversations.show', $conversation);
    }

    public function markRead(Conversation $conversation, ConversationService $conversations)
    {
        /** @var \App\Models\Volunteer $volunteer */
        $volunteer = auth()->guard('front')->user();
        $conversations->assertParticipant($conversation, $volunteer);
        $conversations->markRead($conversation, $volunteer);

        $this->setFlashSession(true, 'Conversation marked as read.');
        return redirect()->back();
    }

    public function markUnread(Conversation $conversation, ConversationService $conversations)
    {
        /** @var \App\Models\Volunteer $volunteer */
        $volunteer = auth()->guard('front')->user();
        $conversations->assertParticipant($conversation, $volunteer);
        $conversations->markUnread($conversation, $volunteer);

        $this->setFlashSession(true, 'Conversation marked as unread.');
        return redirect()->back();
    }

    public function delete(Conversation $conversation, ConversationService $conversations)
    {
        /** @var \App\Models\Volunteer $volunteer */
        $volunteer = auth()->guard('front')->user();
        $conversations->assertParticipant($conversation, $volunteer);
        $conversations->delete($conversation, $volunteer);

        $this->setFlashSession(true, 'Conversation removed from your inbox.');
        return redirect()->route('contact-lists');
    }

    public function bulkAction(Request $request, ConversationService $conversations)
    {
        $validated = $request->validate([
            'action' => 'required|in:read,unread,archive,unarchive,delete',
            'uuids' => 'required|array|min:1',
            'uuids.*' => 'required|string',
        ]);

        /** @var \App\Models\Volunteer $volunteer */
        $volunteer = auth()->guard('front')->user();
        $action = $validated['action'];
        $uuids = array_values(array_unique($validated['uuids']));

        $count = match ($action) {
            'read' => $conversations->bulkMarkRead($uuids, $volunteer),
            'unread' => $conversations->bulkMarkUnread($uuids, $volunteer),
            'archive' => $conversations->bulkArchive($uuids, $volunteer),
            'unarchive' => $conversations->bulkUnarchive($uuids, $volunteer),
            'delete' => $conversations->bulkDelete($uuids, $volunteer),
        };

        $message = match ($action) {
            'read' => "{$count} conversation(s) marked as read.",
            'unread' => "{$count} conversation(s) marked as unread.",
            'archive' => "{$count} conversation(s) archived.",
            'unarchive' => "{$count} conversation(s) unarchived.",
            'delete' => "{$count} conversation(s) removed from your inbox.",
        };

        $this->setFlashSession(true, $message);
        return redirect()->back();
    }

    public function updateThreadPreference(Request $request, Conversation $conversation, ConversationService $conversations)
    {
        $validated = $request->validate([
            'notification_preference' => 'nullable|in:immediate,batch,digest,disabled',
        ]);

        /** @var \App\Models\Volunteer $user */
        $user = auth()->guard('front')->user();
        $conversations->updateThreadPreference(
            $conversation,
            $user,
            $validated['notification_preference'] ?: null
        );

        $this->setFlashSession(true, 'Notification preference updated.');
        return redirect()->route('messaging.conversations.show', $conversation);
    }

    public function updateGlobalPreference(Request $request, ConversationService $conversations)
    {
        $validated = $request->validate([
            'default_delivery' => 'required|in:immediate,batch,digest,disabled',
            'daily_email_limit' => 'required|integer|min:0|max:500',
            'digest_hour' => 'required|integer|min:0|max:23',
            'muted_until' => 'nullable|date',
        ]);

        /** @var \App\Models\Volunteer $user */
        $user = auth()->guard('front')->user();
        $conversations->updateGlobalPreference($user, $validated);

        $this->setFlashSession(true, 'Inbox notification settings updated.');
        return redirect()->route('contact-lists');
    }

    public function verifyPendingContact(string $token, ConversationService $conversations)
    {
        $conversation = $conversations->verifyPendingContact($token);

        $this->setFlashSession(true, 'Your message has been verified and delivered.');
        return redirect()->route('messaging.conversations.show', $conversation);
    }

    public function sendDigests(ConversationNotificationService $notifications)
    {
        $sent = $notifications->sendPendingDigests();

        return response()->json([
            'status' => true,
            'message' => "Conversation digests processed.",
            'sent' => $sent,
        ]);
    }
}
