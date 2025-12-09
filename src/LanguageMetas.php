<?php

/**
 * @author    Joffrey Demetz <joffrey.demetz@gmail.com>
 * @license   MIT License; <https://opensource.org/licenses/MIT>
 */

namespace JDZ\Language;

use JDZ\Language\LanguageException;

class LanguageMetas
{
  /**
   * ISO-Alpha-2 code
   */
  public string $iso;
  public string $tag;
  public string $label;
  public string $code;
  public string $name;
  public ?string $inflector = null;
  public bool $rtl = false;
  public int $firstDay = 1;
  public array $locale;

  public function load(array $data)
  {
    if (empty($data)) {
      throw new LanguageException('No metadata for the Metas object');
    }

    foreach ($data as $property => $value) {
      if (!property_exists($this, $property)) {
        continue;
      }

      $this->{$property} = $value;
    }

    if (false === $this->isValid()) {
      throw new LanguageException('Invalid language metadata');
    }
  }

  public function isValid(): bool
  {
    return $this->iso && $this->tag && $this->label && $this->code && $this->name && $this->locale;
  }
}
