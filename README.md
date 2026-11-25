# Laravel ABN, ACN and TFN Validation Rules

[![tests](https://github.com/BusinessAPI/au-identifiers-laravel/actions/workflows/test.yml/badge.svg)](https://github.com/BusinessAPI/au-identifiers-laravel/actions/workflows/test.yml) [![MIT licence](https://img.shields.io/badge/licence-MIT-blue.svg)](LICENSE)

Validation rules for Australian identifiers in Laravel: ABN, ACN, ARBN, TFN, Director ID and VIN. The checks run offline using the official checksums, so there's no API call and nothing leaves your server.

```bash
composer require businessapi/au-identifiers-laravel
```

The service provider is auto-discovered, so there's nothing to register.

## Use it

As string rules, the way you'd use `email` or `required`:

```php
$request->validate([
    'abn' => ['required', 'abn'],
    'acn' => ['nullable', 'acn'],
    'tfn' => ['required', 'tfn_individual'],
]);
```

Or as Rule objects, handy when you want to pass options:

```php
use BusinessApi\AuIdentifiers\Laravel\Rules\{Abn, Tfn};

$request->validate([
    'abn' => ['required', new Abn],
    'tfn' => ['required', new Tfn(individual: true)],
]);
```

## Rules

| Rule | Checks |
|---|---|
| `abn` | 11 digits, ATO modulus 89 |
| `acn` | 9 digits, ASIC check digit |
| `arbn` | 9 digits, ASIC check digit |
| `tfn` | 8 or 9 digits, ATO modulus 11 (organisations may be 8 digits) |
| `tfn_individual` | 9 digits, ATO modulus 11 (a person's TFN) |
| `director_id` | 15 digits starting with 036 (format only) |
| `vin` | 17 characters, no I, O or Q (ISO 3779) |

Spaces and hyphens are ignored. The Rule objects (`Abn`, `Acn`, `Arbn`, `Tfn`, `DirectorId`, `Vin`) live in `BusinessApi\AuIdentifiers\Laravel\Rules`. Everything is built on [`businessapi/au-identifiers`](https://github.com/BusinessAPI/au-identifiers-php), so the underlying functions are there if you need to validate or format a value outside a request.

## Messages

Default messages read like "The abn must be a valid ABN." To change them, publish the language file and edit it:

```bash
php artisan vendor:publish --tag=au-identifiers-lang
```

That writes `lang/vendor/au-identifiers/en/validation.php`. You can also set a per-field message the usual way:

```php
$request->validate(
    ['abn' => ['required', 'abn']],
    ['abn.abn' => 'That ABN doesn\'t look right.'],
);
```

## Valid isn't registered

These rules only tell you a number is well formed. To check an ABN or ACN is registered and active, or to register companies, ABNs and business names, use the [Business API ABN lookup and company registration API](https://businessapi.com.au).

## Licence

MIT
