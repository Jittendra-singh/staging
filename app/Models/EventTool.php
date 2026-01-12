<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HelperTrait;
class EventTool extends Model
{
    use HasFactory, HelperTrait;
    protected $guarded = ['id'];
    protected $table = 'event_tools';
}
