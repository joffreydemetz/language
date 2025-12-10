<?php

/**
 * @author    Joffrey Demetz <joffrey.demetz@gmail.com>
 * @license   MIT License; <https://opensource.org/licenses/MIT>
 */

namespace JDZ\Language\Tests;

use JDZ\Language\LanguageMetas;
use JDZ\Language\LanguageException;
use PHPUnit\Framework\TestCase;

class LanguageMetasTest extends TestCase
{
    public function testLoadWithValidData(): void
    {
        $metas = new LanguageMetas();
        $data = [
            'iso' => 'en_GB',
            'tag' => 'en-GB',
            'label' => 'English',
            'code' => 'en',
            'name' => 'English (en-GB)',
            'inflector' => 'english',
            'rtl' => false,
            'firstDay' => 1,
            'locale' => ['en_GB.utf8', 'en_GB', 'en']
        ];

        $metas->load($data);

        $this->assertEquals('en_GB', $metas->iso);
        $this->assertEquals('en-GB', $metas->tag);
        $this->assertEquals('English', $metas->label);
        $this->assertEquals('en', $metas->code);
        $this->assertEquals('English (en-GB)', $metas->name);
        $this->assertEquals('english', $metas->inflector);
        $this->assertFalse($metas->rtl);
        $this->assertEquals(1, $metas->firstDay);
        $this->assertEquals(['en_GB.utf8', 'en_GB', 'en'], $metas->locale);
    }

    public function testLoadWithEmptyDataThrowsException(): void
    {
        $this->expectException(LanguageException::class);
        $this->expectExceptionMessage('No metadata for the Metas object');

        $metas = new LanguageMetas();
        $metas->load([]);
    }

    public function testLoadWithMissingRequiredFieldsThrowsException(): void
    {
        $this->expectException(LanguageException::class);
        $this->expectExceptionMessage('Invalid language metadata');

        $metas = new LanguageMetas();
        $metas->load([
            'iso' => 'en_GB',
            'tag' => 'en-GB',
            // Missing other required fields
        ]);
    }

    public function testIsValidReturnsTrueWithCompleteData(): void
    {
        $metas = new LanguageMetas();
        $metas->iso = 'en_GB';
        $metas->tag = 'en-GB';
        $metas->label = 'English';
        $metas->code = 'en';
        $metas->name = 'English (en-GB)';
        $metas->locale = ['en_GB'];

        $this->assertTrue($metas->isValid());
    }

    public function testIsValidReturnsFalseWithIncompleteData(): void
    {
        $metas = new LanguageMetas();
        $metas->iso = 'en_GB';
        $metas->tag = 'en-GB';
        // Missing other required fields

        $this->assertFalse($metas->isValid());
    }

    public function testLoadIgnoresUnknownProperties(): void
    {
        $metas = new LanguageMetas();
        $data = [
            'iso' => 'en_GB',
            'tag' => 'en-GB',
            'label' => 'English',
            'code' => 'en',
            'name' => 'English (en-GB)',
            'locale' => ['en_GB'],
            'unknown_property' => 'should be ignored'
        ];

        $metas->load($data);

        $this->assertEquals('en_GB', $metas->iso);
        $this->assertFalse(property_exists($metas, 'unknown_property'));
    }

    public function testDefaultValues(): void
    {
        $metas = new LanguageMetas();

        $this->assertNull($metas->inflector);
        $this->assertFalse($metas->rtl);
        $this->assertEquals(1, $metas->firstDay);
    }
}
