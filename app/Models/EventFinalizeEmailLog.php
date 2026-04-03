<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\Traits\HelperTrait;

class EventFinalizeEmailLog extends Model
{
    use HasFactory , HelperTrait;    
    protected $table = 'event_finalize_email_logs';    
    protected $fillable = [
        'email_count',
        'is_success',
        'response',
        'response_code',
        'request_data',
        'request_data_size',
    ];
}