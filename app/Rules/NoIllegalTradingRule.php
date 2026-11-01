<?php

namespace App\Rules;

use App\Services\LegalContentFilterService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NoIllegalTradingRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value) || empty(trim($value))) {
            return;
        }

        $filter = app(LegalContentFilterService::class);

        if ($filter->containsProhibitedContent($value)) {
            $violations = $filter->detectViolations($value);
            $words = implode(', ', array_map(fn ($w) => "\"$w\"", $violations));

            $fail("2863 sayılı kanun gereği not alanında alım-satım, definecilik veya dedektör ibaresi ({$words}) kullanılamaz.");
        }
    }
}

