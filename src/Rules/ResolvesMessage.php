<?php

declare(strict_types=1);

namespace BusinessApi\AuIdentifiers\Laravel\Rules;

trait ResolvesMessage
{
    private function fail(string $rule, string $attribute, \Closure $fail): void
    {
        $key = "au-identifiers::validation.$rule";
        $line = trans($key, ['attribute' => $attribute]);

        if ($line === $key) {
            $line = str_replace(':attribute', $attribute, "The :attribute is not a valid $rule.");
        }

        $fail($line);
    }
}
