<?php

declare(strict_types=1);

namespace BusinessApi\AuIdentifiers\Laravel\Tests;

use BusinessApi\AuIdentifiers\Laravel\AuIdentifiersServiceProvider;
use BusinessApi\AuIdentifiers\Laravel\Rules\Abn;
use BusinessApi\AuIdentifiers\Laravel\Rules\Acn;
use BusinessApi\AuIdentifiers\Laravel\Rules\DirectorId;
use BusinessApi\AuIdentifiers\Laravel\Rules\Tfn;
use BusinessApi\AuIdentifiers\Laravel\Rules\Vin;
use Illuminate\Support\Facades\Validator;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class ValidationTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [AuIdentifiersServiceProvider::class];
    }

    #[DataProvider('stringRules')]
    public function testStringRulePasses(string $rule, string $value): void
    {
        $this->assertTrue(
            Validator::make(['field' => $value], ['field' => $rule])->passes(),
            "$rule should pass for $value",
        );
    }

    #[DataProvider('stringRules')]
    public function testStringRuleFailsForGarbage(string $rule, string $value): void
    {
        $this->assertFalse(
            Validator::make(['field' => 'not-a-number'], ['field' => $rule])->passes(),
            "$rule should fail for garbage",
        );
    }

    public static function stringRules(): array
    {
        return [
            'abn' => ['abn', '51 824 753 556'],
            'acn' => ['acn', '004 085 616'],
            'arbn' => ['arbn', '004 085 616'],
            'tfn org 8-digit' => ['tfn', '15195606'],
            'tfn 9-digit' => ['tfn', '123 456 782'],
            'tfn_individual' => ['tfn_individual', '123 456 782'],
            'director_id' => ['director_id', '036 123 456 789 012'],
            'vin' => ['vin', '1HGCM82633A004352'],
        ];
    }

    public function testIndividualTfnRejectsEightDigits(): void
    {
        // An 8-digit organisation TFN is not valid for a person.
        $this->assertTrue(Validator::make(['tfn' => '15195606'], ['tfn' => 'tfn'])->passes());
        $this->assertFalse(Validator::make(['tfn' => '15195606'], ['tfn' => 'tfn_individual'])->passes());
    }

    public function testRuleObjects(): void
    {
        $cases = [
            [new Abn(), '51 824 753 556'],
            [new Acn(), '004 085 616'],
            [new Tfn(), '15195606'],
            [new Tfn(individual: true), '123 456 782'],
            [new DirectorId(), '036 123 456 789 012'],
            [new Vin(), '1HGCM82633A004352'],
        ];

        foreach ($cases as [$rule, $value]) {
            $this->assertTrue(Validator::make(['field' => $value], ['field' => [$rule]])->passes());
            $this->assertFalse(Validator::make(['field' => 'rubbish'], ['field' => [$rule]])->passes());
        }
    }

    public function testObjectTfnRejectsEightDigitsForIndividual(): void
    {
        $this->assertFalse(
            Validator::make(['field' => '15195606'], ['field' => [new Tfn(individual: true)]])->passes(),
        );
    }

    public function testFailureMessageMentionsTheIdentifier(): void
    {
        $v = Validator::make(['company_abn' => 'nope'], ['company_abn' => 'abn']);

        $this->assertTrue($v->fails());
        $this->assertSame('The company abn must be a valid ABN.', $v->errors()->first('company_abn'));
    }
}
