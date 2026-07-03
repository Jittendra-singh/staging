<?php

namespace App\Events;

use App\Models\User;
use App\Models\Volunteer;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdminImpersonationStarted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly User      $admin,
        public readonly Volunteer $target,
        public readonly string    $ipAddress,
        public string             $timestamp = '',
    ) {
        $this->timestamp = now()->toIso8601String();
    }
}
