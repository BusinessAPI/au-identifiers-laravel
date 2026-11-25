<?php

declare(strict_types=1);

namespace BusinessApi\AuIdentifiers\Laravel\Rules;

use BusinessApi\AuIdentifiers\DirectorId as Check;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class DirectorId implements ValidationRule
{
    use ResolvesMessage;

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! Check::isValid($value)) {
            $this->fail('director_id', $attribute, $fail);
        }
    }
}
