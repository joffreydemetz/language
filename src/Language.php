<?php

/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JDZ\Language;

use JDZ\Language\Metas;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\String\Inflector\InflectorInterface;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

/**
 * Language proxy to symfony translator component
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class Language
{
  const FRENCH = 'fr';
  const ENGLISH = 'en';
  const SPANISH = 'es';

  public array $availableLanguages = [self::FRENCH, self::ENGLISH, self::SPANISH];
  public array $languages;
  public string $defaultLang;

  public Metas $metadata;
  public ?InflectorInterface $inflector = null;
  public Translator $translator;

  public function __construct(array $languages = [], ?string $defaultLang = null)
  {
    $this->defaultLang = $defaultLang ?? self::FRENCH;
    $this->languages = $this->determineLanguages($languages);
  }

  /**
   * $lang is a 2 letters code (fr, en, es, ..)
   */
  public function load(string $lang)
  {
    if (!in_array($lang, $this->availableLanguages)) {
      throw new \Exception('Requested language ' . $lang . ' is not available. '
        . 'Choose one of ' . implode(', ', $this->availableLanguages));
    }

    $this->metadata = $this->determineMetadata($lang);

    $this->translator = new Translator($this->metadata->iso);
    $this->translator->addLoader('array', new ArrayLoader());
    $this->translator->addLoader('yaml', new YamlFileLoader());

    $this->inflector = $this->determineInflector($this->metadata->inflector);

    $locale = $this->metadata->locale;
    \array_unshift($locale, \LC_ALL);
    \call_user_func_array('setlocale', $locale);

    return $this;
  }

  protected function determineLanguages(array $languages = []): array
  {
    \array_unshift($languages, $this->defaultLang);

    foreach ($languages as $i => $language) {
      if (!in_array($language, $this->availableLanguages)) {
        unset($language[$i]);
      }
    }

    return \array_unique($languages);
  }

  protected function determineMetadata(string $lang): Metas
  {
    if (false === ($metas = $this->loadLangMetadata($lang))) {
      throw new \Exception('Unable to load language metas for ' . $lang);
    }

    $metadata = new Metas();
    $metadata->load($metas);

    return $metadata;
  }

  protected function determineInflector(?string $inflector): InflectorInterface
  {
    if (null === $inflector) {
      $inflector = 'default';
    }

    $use = '\\JDZ\\Language\\Inflector\\' . ucfirst($inflector) . 'Inflector';

    if (!class_exists($use)) {
      $use = '\\Symfony\\Component\\String\\Inflector\\' . ucfirst($inflector) . 'Inflector';

      if (!class_exists($use)) {
        return $this->determineInflector('default');
      }
    }

    return new $use();
  }

  protected function loadLangMetadata(string $lang): array|false
  {
    try {
      $data = Yaml::parseFile(realpath(__DIR__ . '/../resources/metadata-' . $lang . '.yml'));
      if (empty($data)) {
        return false;
      }
    } catch (ParseException $e) {
      return false;
    }

    return $data;
  }

  /**
   * Load multiple Yaml files 
   */
  public function loadYamlFiles(array $resources, ?string $locale = null, string $domain = 'messages')
  {
    foreach ($resources as $resource) {
      $this->loadYmlFile($resource, $locale, $domain);
    }
    return $this;
  }

  /**
   * Appends strings from a YML file
   * key: value pairs
   */
  public function loadYmlFile(string $resource, ?string $locale = null, string $domain = 'messages')
  {
    if (null === $locale) {
      $locale = $this->metadata->iso;
    }

    $this->translator->addResource('yaml', $resource, $locale, $domain);

    return $this;
  }

  /**
   * Appends strings from an array
   * key +> value pairs
   */
  public function loadArray(array $strings, ?string $locale = null, string $domain = 'messages')
  {
    if (null === $locale) {
      $locale = $this->metadata->iso;
    }

    $this->translator->addResource('array', $strings, $locale, $domain);

    return $this;
  }

  public function get(string $key, array $parameters = [], ?string $default = null): string
  {
    if ('' === $key) {
      return '';
    }

    if (false === $this->has($key) && null !== $default) {
      return $default;
    }

    $string = $this->trans($key, $parameters);
    $string = str_replace(array('\\\\', '\t', '\n'), array("\\", "\t", "\n"), $string);
    return $string;
  }

  public function has(string $key, array $parameters = [], ?string $domain = null, ?string $locale = null): bool
  {
    $trad = $this->trans($key, $parameters, $domain, $locale);
    return $trad !== $key;
  }

  protected function trans(string $key, array $parameters = [], string $domain = null, string $locale = null, int $pass = 1): string
  {
    $string = $this->translator->trans($key, $parameters, $domain, $locale);

    /**
     * replace nested translations
     */
    if (preg_match("/[\*]{3,}([A-Z_]+)[\*]{3,}/", $string)) {
      if ($pass > 5) {
        return $string;
      }

      $string = preg_replace_callback(
        "/[\*]{3,}([A-Z_]+)[\*]{3,}/",
        fn($m) => $this->trans($m[1], $parameters, $domain, $locale, $pass + 1),
        $string
      );
    }

    return $string;
  }

  public function plural(string $key, int $count = 0, array $parameters = [], string $domain = null, string $locale = null): string
  {
    $parameters['%count%'] = $count;
    return $this->get($key, $parameters, $domain, $locale);
  }

  public function pluralize(string $string): string
  {
    $words = $this->inflector->pluralize($string);
    return $words[0];
  }

  public function singularize(string $string): string
  {
    $words = $this->inflector->singularize($string);
    return $words[0];
  }
}
