<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class HexColor implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $pattern = '/^#([a-fA-F0-9]{8})$/';

        if (!preg_match($pattern, $value)) {
            $fail(':attribute должен быть валидным цветом в hex с альфа каналом');
        }
    }
}
