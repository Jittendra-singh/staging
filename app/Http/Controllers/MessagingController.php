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
        $volunteer = auth()->guard('front')->user();
        $showArchived = $request->boolean('archived');
        $search = trim((string) $request->get('search'));

        $conversations = Conversation::forParticipant($volunteer)
            ->with(['latestMessage.sender', 'participants.volunteer'])
            ->whereHas('participants', function ($query) use ($volunteer, $showArchived) {
                $query->where('volunteer_id', $volunteer->id)
                    ->when($showArchived, fn ($q) => $q->whereNotNull('archived_at'))
                    ->when(!$showArchived, fn ($q) => $q->whereNull('archived_at'));
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('subject', 'like', "%{$search}%")
                        ->orWhereHas('messages', fn ($messageQuery) => $messageQuery->where('body', 'like', "%{$search}%"))
                        ->orWhereHas('participants.volunteer', function ($volunteerQuery) use ($search) {
                            $volunteerQuery->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('email_address', 'like', "%{$search}%");
                        });
                });
            })
            ->orderByDesc('last_message_at')
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
            'showArchived' => $showArchived,
            'search' => $search,
            'seo_details' => (object) ['title' => 'Inbox'],
        ]);
    }

    public function show(Conversation $conversation, ConversationService $conversations)
    {
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

    public function store(Request $request, Conversation $conversation, ConversationService $conversations)
    {
        $validated = $request->validate([
            'message' => 'nullable|string|max:20000',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,txt',
        ]);

        $files = $request->file('attachments', []);

        if (empty(trim((string) ($validated['message'] ?? ''))) && empty($files)) {
            return redirect()->back()->withErrors(['message' => 'Please enter a message or attach a file.']);
        }

        $conversations->addMessage(
            $conversation,
            auth()->guard('front')->user(),
            $validated['message'] ?? '',
            $files
        );

        $this->setFlashSession(true, 'Message sent successfully.');
        return redirect()->route('messaging.conversations.show', $conversation);
    }

    public function archive(Conversation $conversation, ConversationService $conversations)
    {
        $conversations->archive($conversation, auth()->guard('front')->user());

        $this->setFlashSession(true, 'Conversation archived.');
        return redirect()->route('contact-lists');
    }

    public function unarchive(Conversation $conversation, ConversationService $conversations)
    {
        $conversations->unarchive($conversation, auth()->guard('front')->user());

        $this->setFlashSession(true, 'Conversation restored.');
        return redirect()->route('messaging.conversations.show', $conversation);
    }

    public function updateThreadPreference(Request $request, Conversation $conversation, ConversationService $conversations)
    {
        $validated = $request->validate([
            'notification_preference' => 'nullable|in:immediate,batch,digest,disabled',
        ]);

        $conversations->updateThreadPreference(
            $conversation,
            auth()->guard('front')->user(),
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

        $conversations->updateGlobalPreference(auth()->guard('front')->user(), $validated);

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
