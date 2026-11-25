<?php

declare(strict_types=1);

namespace BusinessApi\AuIdentifiers\Laravel\Rules;

use BusinessApi\AuIdentifiers\Acn as Check;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class Acn implements ValidationRule
{
    use ResolvesMessage;

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! Check::isValid($value)) {
            $this->fail('acn', $attribute, $fail);
        }
    }
}
