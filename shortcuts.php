<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Translate text
 * 
 * Accepts a variable number of arguments.
 *  i18n($key[, $default]) will translate the key string to text.
 *  i18n($method, $key, $var1, ..) {@see sprintf}
 * 
 * @return string  Translate text.
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
function i18n()
{
  $args = func_get_args();
  
  if ( IAPP === 'cron' ){
    return implode(' ', $args);
  }
  
  if ( count($args) === 1 ){
    $method = '_';
  }
  else {
    $method = array_shift($args);
  }
  
  $methodCall = [ '\JDZ\Language\LanguageHelper', $method ];
  
  if ( !is_callable($methodCall) ){
    throw new \RuntimeException(implode('::', $methodCall).' is not callable');
  }
  
  $lang = Callisto()->getLanguage();
  array_unshift($args, $lang);
  
  return call_user_func_array($methodCall, $args);
}
