<?php

namespace App\Jobs\Messaging;

use App\Models\MessageDeliveryLog;
use App\Services\Messaging\ConversationNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendConversationMessageNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public int $deliveryLogId
    ) {
    }

    public function handle(ConversationNotificationService $notifications): void
    {
        $log = MessageDeliveryLog::find($this->deliveryLogId);

        if (!$log) {
            return;
        }

        $notifications->sendDeliveryLog($log);
    }
}
