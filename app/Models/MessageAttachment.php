<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageAttachment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    public function url(): Attribute
    {
        return Attribute::make(
            get: fn () => asset($this->path),
        );
    }
}
