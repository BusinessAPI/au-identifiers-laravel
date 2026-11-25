<?php

declare(strict_types=1);

namespace BusinessApi\AuIdentifiers\Laravel\Rules;

use BusinessApi\AuIdentifiers\Tfn as Check;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class Tfn implements ValidationRule
{
    use ResolvesMessage;

    // A person's TFN is always 9 digits; organisations may be 8 or 9.
    public function __construct(private readonly bool $individual = false)
    {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! Check::isValid($value, individual: $this->individual)) {
            $this->fail($this->individual ? 'tfn_individual' : 'tfn', $attribute, $fail);
        }
    }
}
