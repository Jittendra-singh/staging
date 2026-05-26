<?php
namespace App\Models;

use App\Models\Traits\HelperTrait;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ReleaseNote extends Model
{
    use HasFactory, HelperTrait;
    protected $guarded = ['id'];
    protected $casts = [
        'release_date' => 'date',
        'published_at' => 'datetime',
    ];

}
