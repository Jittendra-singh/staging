<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    use HasFactory;
    const UPDATED_AT = null;
    protected $primaryKey = 'email';  // assuming 'token' is the unique identifier
    public $incrementing = false;     // if 'token' is not an integer
    protected $table = 'password_reset_tokens';
}
