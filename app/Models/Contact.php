<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\Traits\HelperTrait;
class Contact extends Model
{
    use HasFactory , HelperTrait;
    protected $guarded = ['id'];
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => ucfirst($this->firstname) . ' ' . ucfirst($this->lastname),
        );
    }
}