<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Models\Volunteer;

class RequireFieldsIfEmailExists implements Rule
{
    protected $email;
    protected $field;

    public function __construct($email, $field = null)
    {
        $this->email = $email;
        $this->field = $field;
    }

    public function passes($attribute, $value)
    {
        $emailExists = Volunteer::where('email_address', $this->email)
                         ->exists();

        // If email doesn't exist, the other fields must be filled
        if (!$emailExists && empty($value)) {
            return false;
        }

        return true;
    }

    public function message()
    {
        return "The :attribute field is required when the email is not registered.";
    }
}
