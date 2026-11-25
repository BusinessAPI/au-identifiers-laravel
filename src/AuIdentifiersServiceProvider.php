<?php

declare(strict_types=1);

namespace BusinessApi\AuIdentifiers\Laravel;

use BusinessApi\AuIdentifiers\Abn;
use BusinessApi\AuIdentifiers\Acn;
use BusinessApi\AuIdentifiers\Arbn;
use BusinessApi\AuIdentifiers\DirectorId;
use BusinessApi\AuIdentifiers\Tfn;
use BusinessApi\AuIdentifiers\Vin;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

final class AuIdentifiersServiceProvider extends ServiceProvider
{
    /**
     * String rule name => the check that decides whether a value passes.
     *
     * @var array<string, callable(mixed): bool>
     */
    private const CHECKS = [
        'abn' => [Abn::class, 'isValid'],
        'acn' => [Acn::class, 'isValid'],
        'arbn' => [Arbn::class, 'isValid'],
        'tfn' => [Tfn::class, 'isValid'],
        'director_id' => [DirectorId::class, 'isValid'],
        'vin' => [Vin::class, 'isValid'],
    ];

    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'au-identifiers');

        $this->publishes([
            __DIR__ . '/../lang' => $this->app->langPath('vendor/au-identifiers'),
        ], 'au-identifiers-lang');

        foreach (self::CHECKS as $rule => $check) {
            ValidatorFacade::extend(
                $rule,
                static fn ($attribute, $value): bool => (bool) $check($value),
                $this->message($rule),
            );
        }

        // A person's TFN is always 9 digits, so it needs its own rule.
        ValidatorFacade::extend(
            'tfn_individual',
            static fn ($attribute, $value): bool => Tfn::isValid($value, individual: true),
            $this->message('tfn_individual'),
        );
    }

    private function message(string $rule): string
    {
        $key = "au-identifiers::validation.$rule";
        $line = $this->app['translator']->get($key);

        // Fall back to a plain sentence if the translation files are not loaded (e.g. a bare unit test).
        return $line === $key ? "The :attribute is not a valid $rule." : $line;
    }
}
