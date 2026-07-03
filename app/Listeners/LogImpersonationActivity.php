<?php

namespace App\Listeners;

use App\Events\AdminImpersonationStarted;
use App\Events\AdminImpersonationEnded;
use App\Models\ImpersonationLog;
use Illuminate\Support\Facades\Log;

class LogImpersonationActivity
{
    /**
     * Handle impersonation started event.
     */
    public function handleStarted(AdminImpersonationStarted $event): void
    {
        ImpersonationLog::create([
            'admin_id'         => $event->admin->id,
            'admin_email'      => $event->admin->email,
            'target_user_id'   => $event->target->id,
            'target_email'     => $event->target->email_address,
            'ip_address'       => $event->ipAddress,
            'user_agent'       => request()->userAgent(),
            'action'           => 'started',
            'started_at'       => now(),
        ]);

        Log::info('Impersonation started', [
            'admin_id'       => $event->admin->id,
            'target_user_id' => $event->target->id,
            'ip'             => $event->ipAddress,
        ]);
    }

    /**
     * Handle impersonation ended event.
     */
    public function handleEnded(AdminImpersonationEnded $event): void
    {
        $log = ImpersonationLog::where('admin_id', $event->admin->id)
            ->where('target_user_id', $event->target->id)
            ->whereNull('ended_at')
            ->latest()
            ->first();

        if ($log) {
            $log->update([
                'ended_at'         => now(),
                'duration_seconds' => now()->diffInSeconds($log->started_at),
                'action'           => 'ended',
            ]);
        }

        Log::info('Impersonation ended', [
            'admin_id'       => $event->admin->id,
            'target_user_id' => $event->target->id,
            'duration'       => $log?->duration_seconds,
        ]);
    }
}
