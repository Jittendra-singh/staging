<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImpersonationLog extends Model
{
    protected $fillable = [
        'admin_id',
        'admin_email',
        'target_user_id',
        'target_email',
        'ip_address',
        'user_agent',
        'action',
        'started_at',
        'ended_at',
        'duration_seconds',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function target()
    {
        return $this->belongsTo(Volunteer::class, 'target_user_id');
    }
}
