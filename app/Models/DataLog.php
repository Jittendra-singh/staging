<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\Traits\HelperTrait;

class DataLog extends Model
{
    use HasFactory , HelperTrait;

    public $incrementing = false;
    
    protected $fillable = [
        'type',
        'section',
        'user_id',
        'before_data',
        'after_data',
    ];
}