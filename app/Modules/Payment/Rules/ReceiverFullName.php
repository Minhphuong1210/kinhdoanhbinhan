<?php

namespace App\Modules\Payment\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Tên người nhận phải là họ tên đầy đủ, không phải cách xưng hô.
 */
class ReceiverFullName implements ValidationRule
{
    private const INVALID_FIRST_WORDS = [
        'anh', 'chị', 'chi', 'em', 'ông', 'ong', 'bà', 'ba', 'cô', 'co', 'chú', 'chu',
        'bác', 'bac', 'dì', 'di', 'mẹ', 'me', 'bố', 'bo', 'cha', 'thím', 'thim',
        'mợ', 'mo', 'cậu', 'cau',
    ];

    private const INVALID_PHRASES = [
        'cậu mợ', 'cau mo', 'cô chú', 'co chu', 'ông bà', 'ong ba',
        'ba mẹ', 'ba me', 'bố mẹ', 'bo me', 'cha mẹ', 'cha me',
    ];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $value = trim(preg_replace('/\s+/', ' ', (string) $value));
        $words = explode(' ', $value);

        if (count($words) < 2) {
            $fail('Vui lòng nhập đầy đủ họ và tên.');

            return;
        }

        if (in_array(mb_strtolower($words[0]), self::INVALID_FIRST_WORDS, true)) {
            $fail('Vui lòng nhập đầy đủ họ và tên người nhận, không nhập cách xưng hô.');

            return;
        }

        if (in_array(mb_strtolower($value), self::INVALID_PHRASES, true)) {
            $fail('Vui lòng nhập đầy đủ họ và tên người nhận.');

            return;
        }

        if (!preg_match('/^[\p{L}\s]+$/u', $value)) {
            $fail('Tên người nhận chỉ được chứa chữ cái.');
        }
    }
}
