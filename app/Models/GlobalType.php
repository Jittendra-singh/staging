<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HelperTrait;
class GlobalType extends Model
{
    use HasFactory, HelperTrait;
    protected $table = 'global_types';
    protected $guarded = ['id'];
}
