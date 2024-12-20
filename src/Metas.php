<?php

/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JDZ\Language;

/**
 * Language metas 
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class Metas
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
      throw new \Exception('No metadata for the Metas object');
    }

    foreach ($data as $property => $value) {
      if (!property_exists($this, $property)) {
        throw new \Exception('Could not set ' . $property . ' in Metas object');
      }

      $this->{$property} = $value;
    }

    if (false === $this->isValid()) {
      throw new \Exception('Invalid language metadata');
    }
  }

  public function isValid(): bool
  {
    return $this->iso && $this->tag && $this->label && $this->code && $this->name && $this->locale;
  }
}
