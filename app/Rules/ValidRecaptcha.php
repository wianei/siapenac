<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Http\Controllers\Log\LogController;
use Illuminate\Support\Facades\DB;

class ValidRecaptcha implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
        $recaptcha_secret = '6LdogMEZAAAAAPn-0QfyRM10rpBuP0ZHItFWQC6X';
        $recaptcha_response = $value;
        $recaptcha = file_get_contents($recaptcha_url . '?secret=' . urlencode($recaptcha_secret) . '&response=' . urlencode($recaptcha_response));
        return json_decode($recaptcha)->success;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Validação não aprovada.';
    }
}
