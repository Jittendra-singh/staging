<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\Traits\HelperTrait;

class CustomerSearch extends Model
{
    use HasFactory , HelperTrait;

    public $incrementing = false;
    protected $table =  'customer_searches';
    
    protected $fillable = [
        'start_date',
        'end_date',
        'category',
    ];
}