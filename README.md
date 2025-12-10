# JDZ Language 

`Language` is a proxy to the `symfony/translation` component - a modular PHP package designed for managing and translating multilingual content in your web projects. This project simplifies the use of language files and provides a clear API for easily handling translations.

## Features

- Built on the **symfony/translation** component for robust translation capabilities.
- Uses the **symfony/string** inflector for pluralization and singularization. 
- **LanguageCode** enum for type-safe language code handling.
- Custom **LanguageException** for language-specific error handling.
- Load default or application-specific language files.
- Flexible management of translation keys.
- Support for YAML format language files.
- Easy integration with other frameworks or custom solutions.
- Optimized for fast performance and maximum extensibility.
- Inflector support for French, English & Spanish (introduced in symfony/string 7.2, requires PHP >= 8.2).

## Installation

Add the package to your project using **Composer**:

```bash
composer require jdz/language
```

## Requirements

- PHP 8.2 or higher
- symfony/translation
- symfony/string
- symfony/yaml

## Usage

For a complete example, check the `example` folder in the repository.

### Initialization

```php
use JDZ\Language\Language;

$language = new Language(
    // app languages
    ['fr', 'en'],
    // default language
    'fr'
);
```

### Load User Language

If not an available language, it falls back to the default language.

```php
$language->load('fr');
```

### Load Translations from Array

```php
$language->loadArray([
    'welcome_message' => 'Hi !',
    'test' => [
        'key1' => 'Test 1 nested',
        'key2' => 'Test 1 nested 2',
    ],
    'test2' => 'Test 2',
]);
```

### Load Translations from YAML File

```php
$language->loadYamlFiles([
    __DIR__ . '/file1.yml',
    __DIR__ . '/file2.yml',
]);
$language->loadYamlFile(__DIR__ . '/file3.yml');
```

Example YAML file:

```yaml
welcome_message: "Welcome"
goodbye_message: "Goodbye"
```

### Setting Values

```php
$language->set('custom.key', 'My custom value');
```

### Getting Values

```php
$welcomeMessage = $language->get('welcome_message');
// Hi !

$customMessage = $language->get('custom.key');
// My custom value

$notDefined = $language->get('test.me');
// test.me (returns key if not found)

$notDefinedButDefault = $language->get('test.me', [], 'Default value');
// Default value
```

### Using LanguageCode Enum

```php
use JDZ\Language\LanguageCode;

// Check if a language code is valid
if (LanguageCode::isValid('fr')) {
    // Valid language
}

// Get language from enum
$french = LanguageCode::FRENCH;
echo $french->value; // 'fr'

// Try to get enum from string
$lang = LanguageCode::tryFrom('en'); // Returns LanguageCode::ENGLISH or null
```

## API Reference

### Language Class Methods

| Method            | Description |
|-------------------|-------------|
| `load(string $lang)` | Load a user language. Throws `LanguageException` if invalid. |
| `loadYamlFiles(array $resources)` | Load translations from an array of YAML files. |
| `loadYamlFile(string $resource)` | Load translations from a single YAML file. |
| `loadArray(array $strings)` | Load translations from an array of key => value pairs. |
| `set(string $key, mixed $value)` | Adds a translation. |
| `get(string $key, array $parameters = [], ?string $default = null)` | Retrieves a translation at the specified path. |
| `has(string $key)` | Checks if a translation exists at the specified path. |
| `plural(string $key, int $count)` | Load a plural translation with count parameter. |
| `pluralize(string $string)` | Uses the symfony/string inflector to pluralize a word. |
| `singularize(string $string)` | Uses the symfony/string inflector to singularize a word. |

### LanguageCode Enum

Available language codes:
- `LanguageCode::FRENCH` (value: 'fr')
- `LanguageCode::ENGLISH` (value: 'en')
- `LanguageCode::SPANISH` (value: 'es')

Methods:
- `LanguageCode::isValid(string $value): bool` - Check if a string is a valid language code

### LanguageException

Custom exception class for language-specific errors. Extends `\Exception`.

## Testing

The package includes a comprehensive test suite with 38 tests covering all functionality.

To run the tests:

```bash
composer test

# For detailed test output:
composer test -- --testdox
```

Or directly with PHPUnit:

```bash
vendor/bin/phpunit --colors=always --testdox
```

## License

This project is licensed under the MIT License - see the LICENSE file for details.
