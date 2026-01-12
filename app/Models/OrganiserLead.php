<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganiserLead extends Model
{
    use HasFactory;
    protected $table = "organiser_contacts";
    protected $guarded = ["id"];
}
