<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventWaitlist extends Model
{
    protected $table = 'event_waitlists';
    protected $fillable = ['event_id', 'ticket_id', 'first_name', 'last_name', 'email', 'status', 'joined_at'];

    public function ticket() {
        return $this->belongsTo(\App\Models\EventTicket::class, 'ticket_id');
    }
}
