# Language

`Language` is a proxy I use to `symfony/translation` component is a modular PHP package designed for managing and translating multilingual content in your web projects. This project simplifies the use of language files and provides a clear API for easily handling translations.

## Features

- Built on the **symfony/translation** component for robust translation capabilities.
- Uses the **symfony/string** inflector for pluralize and singularize. 
- Load default or application-specific language files.
- Flexible management of translation keys.
- Support for YAML format language files.
- Easy integration with other frameworks or custom solutions.
- Optimized for fast performance and maximum extensibility.
- Inflector for French, English & soon Spanish -> introduced in symfony/string 7.2 (PHP >= 8.2).

## Installation

Add the package to your project using **Composer**:

```bash
composer require jdz/language
```

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

### load user language
if not an available languages it falls back to the default language

```php
$language->load('fr');
```

### load translations from array

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

### load translations from YAML file

```php
$language->loadYmlFiles([
    __DIR__ . '/file1.yml',
    __DIR__ . '/file2.yml',
]);
$language->loadYmlFile(__DIR__ . '/file3.yml');
```

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
// null
$notDefinedButDefault = $language->get('test.me', 'Default value');
// Default value
```

## Methods

| Method            | Description |
|-------------------|-------------|
| `load()`          | Load a user language. |
| `loadYamlFiles()` | Load translations from an array of YAML files. |
| `loadYamlFile()`  | Load translations from a YAML file. |
| `loadArray()`     | Load translations from an array of key => value pairs. |
| `set()`           | Adds a translation. |
| `get()`           | Retrieves a translation at the specified path. |
| `has()`           | Checks if a translation exists at the specified path. |
| `plural()`        | Load a plural. |
| `pluralize()`     | Uses the symfony/string inflector. |
| `pluralize()`     | Uses the symfony/string inflector. |

## License

This project is licensed under the MIT License. See the LICENSE file for details.

## Author

(c) Joffrey Demetz <joffrey.demetz@gmail.com>
