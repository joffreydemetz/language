<?php

/**
 * @author    Joffrey Demetz <joffrey.demetz@gmail.com>
 * @license   MIT License; <https://opensource.org/licenses/MIT>
 */

namespace JDZ\Language\Tests;

use JDZ\Language\Language;
use JDZ\Language\LanguageCode;
use JDZ\Language\LanguageException;
use PHPUnit\Framework\TestCase;

class LanguageTest extends TestCase
{
    public function testConstructorWithDefaultLanguage(): void
    {
        $language = new Language();

        $this->assertEquals('fr', $language->defaultLang);
        $this->assertContains('fr', $language->languages);
    }

    public function testConstructorWithCustomDefaultLanguage(): void
    {
        $language = new Language([], 'en');

        $this->assertEquals('en', $language->defaultLang);
        $this->assertContains('en', $language->languages);
    }

    public function testConstructorWithMultipleLanguages(): void
    {
        $language = new Language(['en', 'es'], 'fr');

        $this->assertEquals('fr', $language->defaultLang);
        $this->assertContains('fr', $language->languages);
        $this->assertContains('en', $language->languages);
        $this->assertContains('es', $language->languages);
    }

    public function testConstructorFiltersInvalidLanguages(): void
    {
        $language = new Language(['en', 'invalid', 'es'], 'fr');

        $this->assertContains('fr', $language->languages);
        $this->assertContains('en', $language->languages);
        $this->assertContains('es', $language->languages);
        $this->assertNotContains('invalid', $language->languages);
    }

    public function testLoadWithValidLanguage(): void
    {
        $language = new Language();
        $result = $language->load('en');

        $this->assertSame($language, $result);
        $this->assertNotNull($language->metadata);
        $this->assertNotNull($language->translator);
        $this->assertNotNull($language->inflector);
    }

    public function testLoadWithInvalidLanguageThrowsException(): void
    {
        $this->expectException(LanguageException::class);
        $this->expectExceptionMessageMatches('/Requested language .* is not available/');

        $language = new Language();
        $language->load('invalid');
    }

    public function testLoadArrayAddsTranslations(): void
    {
        $language = new Language();
        $language->load('en');
        $language->loadArray([
            'test.key' => 'Test Value',
            'another.key' => 'Another Value'
        ]);

        $this->assertEquals('Test Value', $language->get('test.key'));
        $this->assertEquals('Another Value', $language->get('another.key'));
    }

    public function testGetReturnsTranslation(): void
    {
        $language = new Language();
        $language->load('en');
        $language->loadArray(['greeting' => 'Hello']);

        $this->assertEquals('Hello', $language->get('greeting'));
    }

    public function testGetReturnsDefaultForMissingKey(): void
    {
        $language = new Language();
        $language->load('en');

        $this->assertEquals('default value', $language->get('missing.key', [], 'default value'));
    }

    public function testGetReturnsEmptyStringForEmptyKey(): void
    {
        $language = new Language();
        $language->load('en');

        $this->assertEquals('', $language->get(''));
    }

    public function testGetWithParameters(): void
    {
        $language = new Language();
        $language->load('en');
        $language->loadArray(['greeting' => 'Hello %name%']);

        $this->assertEquals('Hello John', $language->get('greeting', ['%name%' => 'John']));
    }

    public function testHasReturnsTrueForExistingKey(): void
    {
        $language = new Language();
        $language->load('en');
        $language->loadArray(['existing' => 'value']);

        $this->assertTrue($language->has('existing'));
    }

    public function testHasReturnsFalseForMissingKey(): void
    {
        $language = new Language();
        $language->load('en');

        $this->assertFalse($language->has('missing.key'));
    }

    public function testSetAddsTranslation(): void
    {
        $language = new Language();
        $language->load('en');
        $result = $language->set('new.key', 'New Value');

        $this->assertSame($language, $result);
        $this->assertEquals('New Value', $language->get('new.key'));
    }

    public function testPluralWithCount(): void
    {
        $language = new Language();
        $language->load('en');
        $language->loadArray(['items' => 'There are %count% items']);

        $result = $language->plural('items', 5);

        $this->assertStringContainsString('5', $result);
    }

    public function testPluralize(): void
    {
        $language = new Language();
        $language->load('en');

        $this->assertEquals('words', $language->pluralize('word'));
        $this->assertEquals('items', $language->pluralize('item'));
    }

    public function testSingularize(): void
    {
        $language = new Language();
        $language->load('en');

        $this->assertEquals('word', $language->singularize('words'));
        $this->assertEquals('item', $language->singularize('items'));
    }

    public function testLoadYamlFilesChaining(): void
    {
        $language = new Language();
        $language->load('en');

        $result = $language->loadYamlFiles([]);

        $this->assertSame($language, $result);
    }

    public function testLoadYmlFileChaining(): void
    {
        $language = new Language();
        $language->load('en');

        // Create a temporary YAML file for testing
        $tempFile = sys_get_temp_dir() . '/test_lang.yml';
        file_put_contents($tempFile, "test_key: Test Value\n");

        try {
            $result = $language->loadYmlFile($tempFile);

            $this->assertSame($language, $result);
            $this->assertEquals('Test Value', $language->get('test_key'));
        } finally {
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
    }

    public function testGetWithEscapeSequences(): void
    {
        $language = new Language();
        $language->load('en');
        $language->loadArray(['escaped' => 'Line 1\\nLine 2\\tTabbed']);

        $result = $language->get('escaped');

        $this->assertStringContainsString("\n", $result);
        $this->assertStringContainsString("\t", $result);
    }
}
