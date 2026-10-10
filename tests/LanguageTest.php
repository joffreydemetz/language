<?php

/**
 * @author    Joffrey Demetz <joffrey.demetz@gmail.com>
 * @license   MIT License; <https://opensource.org/licenses/MIT>
 */

namespace JDZ\Language\Tests;

use JDZ\Language\Language;
use JDZ\Language\LanguageCode;
use JDZ\Language\LanguageException;
use PHPUnit\Framework\Attributes\DataProvider;
use JDZ\Language\Inflector\DefaultInflector;
use PHPUnit\Framework\TestCase;

class LanguageTest extends TestCase
{
    private string|false $locale;

    /** @var string[] */
    private array $tmpFiles = [];

    protected function setUp(): void
    {
        // load() calls setlocale(LC_ALL, …) for the whole process
        $this->locale = setlocale(LC_ALL, 0);
    }

    protected function tearDown(): void
    {
        if (false !== $this->locale) {
            setlocale(LC_ALL, $this->locale);
        }

        foreach ($this->tmpFiles as $path) {
            if (is_file($path)) {
                unlink($path);
            }
        }
        $this->tmpFiles = [];
    }

    private function yamlFile(string $yaml): string
    {
        $path = sys_get_temp_dir() . '/jdz-language-' . uniqid('', true) . '.yml';
        file_put_contents($path, $yaml);
        $this->tmpFiles[] = $path;

        return $path;
    }

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

    public function testPluralUsesDomain(): void
    {
        $language = new Language();
        $language->load('en');
        $language->loadArray(['items' => 'There are %count% shop items'], null, 'shop');

        $this->assertEquals('There are 3 shop items', $language->plural('items', 3, [], 'shop'));
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

    public function testLoadYamlFilesLoadsEveryFileIntoTheDomainAndLocaleGiven(): void
    {
        $language = (new Language())->load('en');

        $result = $language->loadYamlFiles([$this->yamlFile("a: A\n"), $this->yamlFile("b: B\n")], 'fr', 'admin');

        $this->assertSame($language, $result);
        $this->assertFalse($language->has('a'));
        $this->assertTrue($language->has('a', [], 'admin', 'fr'));
        $this->assertTrue($language->has('b', [], 'admin', 'fr'));
    }

    public function testLoadYmlFileChaining(): void
    {
        $language = new Language();
        $language->load('en');

        $result = $language->loadYmlFile($this->yamlFile("test_key: Test Value\n"));

        $this->assertSame($language, $result);
        $this->assertEquals('Test Value', $language->get('test_key'));
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


    public static function languageCodes(): array
    {
        return [
            'fr' => ['fr', 'fr_FR', 'cheval', 'chevaux'],
            'en' => ['en', 'en_GB', 'horse', 'horses'],
            'es' => ['es', 'es_ES', 'caballo', 'caballos'],
        ];
    }

    /**
     * Every code has its metadata file and a real inflector (the default one pluralizes nothing).
     */
    #[DataProvider('languageCodes')]
    public function testEveryLanguageLoadsItsMetadataAndInflector(string $code, string $iso, string $singular, string $plural): void
    {
        $language = (new Language())->load($code);

        $this->assertSame($iso, $language->metadata->iso);
        $this->assertSame($plural, $language->pluralize($singular));
    }

    public function testAnUnknownInflectorFallsBackToTheDefaultOne(): void
    {
        $language = new class extends Language {
            protected function loadLangMetadata(string $lang): array|false
            {
                $metas = parent::loadLangMetadata($lang);
                $metas['inflector'] = 'klingon';

                return $metas;
            }
        };
        $language->load('en');

        $this->assertInstanceOf(DefaultInflector::class, $language->inflector);
        $this->assertSame('horse', $language->pluralize('horse'));
        $this->assertSame('horses', $language->singularize('horses'));
    }

    public function testMissingMetadataIsALanguageException(): void
    {
        $language = new class extends Language {
            protected function loadLangMetadata(string $lang): array|false
            {
                return false;
            }
        };

        $this->expectException(LanguageException::class);
        $this->expectExceptionMessage('Unable to load language metas for en');
        $language->load('en');
    }

    private static function placeholders(): Language
    {
        return (new Language())->load('en')->loadArray([
            'GREETING' => 'Hello ***NAME***',
            'NAME' => '***FIRST*** Doe',
            'FIRST' => 'John',
            'LOOP' => 'x ***LOOP***',
            'GHOST' => 'see ***MISSING***',
        ]);
    }

    public static function placeholderCases(): array
    {
        return [
            'nested two levels' => ['GREETING', 'Hello John Doe'],
            'a self-reference stops after five passes' => ['LOOP', 'x x x x x x ***LOOP***'],
            'a missing key leaves its name' => ['GHOST', 'see MISSING'],
        ];
    }

    #[DataProvider('placeholderCases')]
    public function testPlaceholdersExpandRecursively(string $key, string $expected): void
    {
        $this->assertSame($expected, self::placeholders()->get($key));
    }

    public static function getIfCases(): array
    {
        return [
            'a known key is translated' => ['GREETING', null, 'Hello John Doe'],
            'an unknown key without default comes back' => ['nope', null, 'nope'],
            'an unknown key takes the default' => ['nope', 'dflt', 'dflt'],
            'an empty key takes the default' => ['', 'dflt', 'dflt'],
            'an empty key without default is empty' => ['', null, ''],
        ];
    }

    #[DataProvider('getIfCases')]
    public function testGetIf(string $key, ?string $default, string $expected): void
    {
        $this->assertSame($expected, self::placeholders()->getIf($key, [], $default));
    }

    public function testHasLooksInTheDomainAndLocaleAsked(): void
    {
        $language = (new Language())->load('en');
        $language->set('k', 'v', 'fr', 'admin');

        $this->assertFalse($language->has('k'));
        $this->assertFalse($language->has('k', [], 'admin'));
        $this->assertFalse($language->has('k', [], null, 'fr'));
        $this->assertTrue($language->has('k', [], 'admin', 'fr'));
    }
}
