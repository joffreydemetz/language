<?php

/**
 * @author    Joffrey Demetz <joffrey.demetz@gmail.com>
 * @license   MIT License; <https://opensource.org/licenses/MIT>
 */

namespace JDZ\Language;

enum LanguageCode: string
{
  case FRENCH = 'fr';
  case ENGLISH = 'en';
  case SPANISH = 'es';

  /**
   * Check if a string value is a valid language code
   * 
   * @param string $value
   * @return bool
   */
  public static function isValid(string $value): bool
  {
    return self::tryFrom($value) !== null;
  }
}
