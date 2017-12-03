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
   * Extract translations from INI file
   *
   * @param   string    $path       The path of ini file
   * @return   array     Key/Value pairs of translations
   */
  public static function parseIniFile($path)
  {
    $strings = [];
    
    if ( file_exists($path) ){
      $php_errormsg = null;
      $track_errors = ini_get('track_errors');
      ini_set('track_errors', true);
      
      $contents = file_get_contents($path);
      $contents = str_replace('_QQ_', '"', $contents);
      $strings  = parse_ini_string($contents, false, INI_SCANNER_RAW);
      
      $clean=[];
      foreach($strings as $key => $value){
        if ( substr($key, 0, 1) === '_' ){
          $key = substr($key, 1);
        }
        $clean[$key] = $value;
      }
      $strings = $clean;
      
      ini_set('track_errors', $track_errors);
      
      return $strings;
    }
    
    return $strings;
  }
  
  /**
   * Translates a string into the current language.
   * 
   * @param   Language  $lang       The loaded language instance
   * @param   string    $string     The string to translate
   * @return   string    The translated string.
   */
  public static function _(Language $lang, $string)
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
    
    return $lang->_($string);
  }
  
  /**
   * Like Text::sprintf but tries to pluralise the string.
   * 
   * Behaves like the sprintf function.
   * 
   * @param   Language  $lang     The loaded language instance
   * @param   string    $string   The base string
   * @param   integer   $n        The number of items
   * @return   string    The translated string
   */
  public static function plural(Language $lang, $string, $n=0)
  {
    $args = func_get_args();
    array_shift($args);
    return call_user_func_array([$lang, 'plural'], $args);
  }
  
  /**
   * Passes a string thru a sprintf.
   * 
   * Note that this method can take a mixed number of arguments as for the sprintf function.
   * 
   * @param   Language  $lang     The loaded language instance
   * @param   string    $string   The base string
   * @return   string    The translated string
   */
  public static function sprintf(Language $lang, $string)
  {
    $args = func_get_args();
    array_shift($args);
    return call_user_func_array([$lang, 'sprintf'], $args);
  }
  
  /**
   * Passes a string thru an printf.
   * 
   * Note that this method can take a mixed number of arguments as for the sprintf function.
   * 
   * @param   Language  $lang     The loaded language instance
   * @param   string    $string   The base string
   * @return   string    The translated string
   */
  public static function printf(Language $lang, $string)
  {
    $args = func_get_args();
    array_shift($args);
    return call_user_func_array([$lang, 'printf'], $args);
  }
  
  /**
   * Return strtolower string
   * 
   * @param   string  $string  The base string
   * @return  string 
   */
  public static function strtolower(Language $lang, $string)
  { 
    return $lang->strtolower($string);
  }
  
  /**
   * Return strtoupper string
   * 
   * @param   string  $string  The base string
   * @return  string 
   */
  public static function strtoupper(Language $lang, $string)
  { 
    return $lang->strtoupper($string);
  }
  
  /**
   * Return ucfirst string
   * 
   * @param   string  $string  The base string
   * @return  string 
   */
  public static function ucfirst(Language $lang, $string)
  { 
    return $lang->ucfirst($string);
  }
  
  /**
   * Return lcfirst string
   * 
   * @param   string  $string  The base string
   * @return  string 
   */
  public static function lcfirst(Language $lang, $string)
  { 
    return $lang->lcfirst($string);
  }
  
  /**
   * Return ucwords string
   * 
   * @param   string  $string  The base string
   * @return  string 
   */
  public static function ucwords(Language $lang, $string)
  { 
    return $lang->ucwords($string);
  }  
} 
  
