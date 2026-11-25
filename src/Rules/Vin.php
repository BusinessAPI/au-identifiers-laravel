<?php

declare(strict_types=1);

namespace BusinessApi\AuIdentifiers\Laravel\Rules;

use BusinessApi\AuIdentifiers\Vin as Check;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class Vin implements ValidationRule
{
    use ResolvesMessage;

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! Check::isValid($value)) {
            $this->fail('vin', $attribute, $fail);
        }
    }
}
