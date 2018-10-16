<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Language;

/**
 * Language Helper
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
abstract class LanguageHelper 
{
  /**
   * Translates a string into the current language.
   * 
   * @param   Language  $lang       The loaded language instance
   * @param   string    $string     The string to translate
   * @return   string    The translated string.
   */
  public static function _(Language $lang, string $string, array $parameters=[])
  {
    // check for coma separated keys    
    if ( strpos($string, ',') !== false ){
      $test = substr($string, strpos($string, ','));
      
      // translate coma separated keys
      if ( strtoupper($test) === $test ){
        $strs = explode(',', $string);
        foreach($strs as $i => $str){
          $strs[$i] = $lang->_($str);
        }
        
        $str = array_shift($strs);
        $str = preg_replace('/\[\[%([0-9]+):[^\]]*\]\]/', '%\1$s', $str);
        $str = vsprintf($str, $strs);
        
        return $str;
      }
    }
    
    return $lang->_($string, $parameters);
  }
  
  /**
   * Pluralises a string
   * 
   * @param  Language  $lang        The loaded language instance
   * @param  string    $string      The base string
   * @param  int       $n           The number of items
   * @param  array     $parameters  Optionnal parameters
   * @return string    The translated string
   */
  public static function plural(Language $lang, string $string, $n=0, array $parameters=[])
  {
    $args = func_get_args();
    array_shift($args);
    return call_user_func_array([$lang, 'plural'], $args);
  }
} 
