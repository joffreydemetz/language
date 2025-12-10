<?php

/**
 * @author    Joffrey Demetz <joffrey.demetz@gmail.com>
 * @license   MIT License; <https://opensource.org/licenses/MIT>
 */

namespace JDZ\Language\Tests;

use JDZ\Language\LanguageCode;
use PHPUnit\Framework\TestCase;

class LanguageCodeTest extends TestCase
{
    public function testEnumCases(): void
    {
        $this->assertEquals('fr', LanguageCode::FRENCH->value);
        $this->assertEquals('en', LanguageCode::ENGLISH->value);
        $this->assertEquals('es', LanguageCode::SPANISH->value);
    }

    public function testCasesReturnsAllLanguages(): void
    {
        $cases = LanguageCode::cases();

        $this->assertCount(3, $cases);
        $this->assertContains(LanguageCode::FRENCH, $cases);
        $this->assertContains(LanguageCode::ENGLISH, $cases);
        $this->assertContains(LanguageCode::SPANISH, $cases);
    }

    public function testIsValidWithValidLanguages(): void
    {
        $this->assertTrue(LanguageCode::isValid('fr'));
        $this->assertTrue(LanguageCode::isValid('en'));
        $this->assertTrue(LanguageCode::isValid('es'));
    }

    public function testIsValidWithInvalidLanguage(): void
    {
        $this->assertFalse(LanguageCode::isValid('de'));
        $this->assertFalse(LanguageCode::isValid('it'));
        $this->assertFalse(LanguageCode::isValid('invalid'));
        $this->assertFalse(LanguageCode::isValid(''));
    }

    public function testTryFromWithValidLanguage(): void
    {
        $this->assertSame(LanguageCode::FRENCH, LanguageCode::tryFrom('fr'));
        $this->assertSame(LanguageCode::ENGLISH, LanguageCode::tryFrom('en'));
        $this->assertSame(LanguageCode::SPANISH, LanguageCode::tryFrom('es'));
    }

    public function testTryFromWithInvalidLanguage(): void
    {
        $this->assertNull(LanguageCode::tryFrom('invalid'));
    }

    public function testFromWithValidLanguage(): void
    {
        $this->assertSame(LanguageCode::FRENCH, LanguageCode::from('fr'));
        $this->assertSame(LanguageCode::ENGLISH, LanguageCode::from('en'));
        $this->assertSame(LanguageCode::SPANISH, LanguageCode::from('es'));
    }

    public function testFromWithInvalidLanguageThrowsException(): void
    {
        $this->expectException(\ValueError::class);
        LanguageCode::from('invalid');
    }
}
