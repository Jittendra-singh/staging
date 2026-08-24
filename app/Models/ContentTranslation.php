<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentTranslation extends Model
{
    protected $fillable = [
        'model_type',
        'model_id',
        'field',
        'locale',
        'source_hash',
        'source_text',
        'translated_text',
        'provider',
    ];
}
