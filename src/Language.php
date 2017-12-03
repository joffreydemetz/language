<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Language;

/**
 * Language Base Object
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class Language 
{
  /**
   * Loaded language code
   * 
   * @var string
   */
  public $lang;
  
  /**
   * Language locale
   * 
   * @var array
   */
  public $locale;
  
  /**
   * Language tag
   * 
   * @var string
   */
  public $tag;
  
  /**
   * Language code
   * 
   * @var string
   */
  public $code;
  
  /**
   * Language name
   * 
   * @var string
   */
  public $name;
  
  /**
   * Language is right to left
   * 
   * @var bool
   */
  public $rtl;
  
  /**
   * Language first day
   * 
   * @var int
   */
  public $firstDay;
  
  /**
   * Default language code
   * 
   * @var string
   */
  protected static $default = 'fr-FR';
  
  /**
   * Language loaded strings
   * 
   * @var array
   */
  protected $strings;
  
  
  /**
   * Loaded instance
   * 
   * @var Language
   */
  protected static $instance;
  
  /**
   * Constructor
   * 
   * @param   string|null   $lang   Language code or null to use default
   */
  public static function getInstance($lang=null)
  {
    if ( !isset(self::$instance) ){
      self::$instance = new Language($lang);
    }
    
    return self::$instance;
  }
  
  /**
   * Constructor
   * 
   * @param   string|null   $lang   Language code or null to use default
   */
  public function __construct($lang=null)
  {
    if ( $lang === null ){
      $lang = $this->default;
    }
    
    $this->lang     = $lang;
    $this->name     = 'French (fr-FR)';
    $this->tag      = 'fr-FR';
    $this->code     = 'fr';
    $this->rtl      = false;
    $this->locale   = [ 'fr_FR.utf8', 'fr_FR.UTF-8', 'fr_FR.UTF-8@euro', 'fr_FR', 'fre_FR', 'fr', 'france' ];
    $this->firstDay = 1;
    $this->strings  = [];
  }
  
  /**
   * Set the locale config
   *
   * @param   array     $metadata   Key/value pairs
   * @return   void
   */
  public function setMetadata(array $metadata=[])
  {
    foreach($metadata as $key => $value){
      $this->{$key} = $value;
    }
  }
  
  /**
   * Appends the strings to the existing strings
   *
   * @param   array     $strings   Key/value pairs of strings 
   * @return   void
   */
  public function add(array $strings=[])
  {
    $this->strings = array_merge($this->strings, $strings);
  }
  
  /**
   * Loads strings for javascript
   *
   * @param   string    $keys       Text keys to return.
   * @param   bool      $defaults   True to default the result to the translation key, False to default to an empty string.
   * @return   array     Array of translations
   */
  public function jsStrings($keys, $defaults=false)
  {
    $keys = urldecode($keys);
    $keys = $keys !== '' ? explode(',', $keys) : [];
    
    $data = [];
    
    if ( count($keys) ){
      foreach($keys as $key){
        $key = strtoupper($key);
        $default = $defaults === true ? $key : '';
        $data[$key] = $this->hasKey($key) ? $this->_($key) : $default;
      }      
    }
    
    return $data;
  }
  
  /**
   * Loads strings for help
   *
   * @param   string    $keys       Text keys to return.
   * @return   array     Array of translations
    */
  public function getHelp(array $keys=[])
  {
    $data = [];
    
    if ( count($keys) ){
      $lang = Callisto()->language;
      foreach($keys as $key){
        $key = strtoupper($key);
        
        $data[] = (object)[
          'key'   => $key,
          'label' => $lang->hasKey($key.'_LABEL') ? $lang->_($key.'_LABEL') : '',
          'desc'  => $lang->hasKey($key) ? $lang->_($key) : '',
        ];
      }      
    }
    
    return $data;
  }
  
  /**
   * Translate function, mimics the php gettext (alias _) function.
   *
   * @param   string   $string                The string to translate
   * @return   string  The translation of the string
   */
  public function _($string)
  {
    if ( $string == '' ){
      return '';
    }

    $key = strtoupper($string);

    if ( isset($this->strings[$key]) ){
      $string = $this->strings[$key];
    }
    
    // Interpret \n and \t characters
    $string = str_replace(array('\\\\', '\t', '\n'), array("\\", "\t", "\n"), $string);
    
    return $string;
  }
  
  /**
   * Return strtolower string
   * 
   * @param   string  $string  The base string
   * @return  string 
   */
  public function strtolower($string)
  { 
    $string = $this->_($string);
    return mb_strtolower($string);
  }
  
  /**
   * Return strtoupper string
   * 
   * @param   string  $string  The base string
   * @return  string 
   */
  public function strtoupper($string)
  { 
    $string = $this->_($string);
    return mb_strtoupper($string);
  }
  
  /**
   * Return ucfirst string
   * 
   * @param   string  $string  The base string
   * @return  string 
   */
  public function ucfirst($string)
  { 
    $string = $this->_($string);
    return ucfirst($string);
  }
  
  /**
   * Return lcfirst string
   * 
   * @param   string  $string  The base string
   * @return  string 
   */
  public function lcfirst($string)
  { 
    $string = $this->_($string);
    return lcfirst($string);
  }
  
  /**
   * Return ucwords string
   * 
   * @param   string  $string  The base string
   * @return  string 
   */
  public function ucwords($string)
  { 
    $string = $this->_($string);
    return ucwords($string);
  }
  
  /**
   * Like sprintf but tries to pluralise the string
   * 
   * Behaves like the sprintf function
   * 
   * @param   string  $string  The base string
   * @param   int     $n       The number of items
   * @return  string  The translated string
   */
  public function plural($string, $n=0)
  { 
    $args  = func_get_args();
    $count = count($args);
    
    // Try the key from the language plural potential suffixes
    $found = false;
    $suffixes = $this->getPluralSuffixes((int)$n);
    array_unshift($suffixes, (int)$n);
    
    foreach($suffixes as $suffix){
      $key = $string.'_'.$suffix;
      if ( $this->hasKey($key) ){
        $found = true;
        break;
      }
    }
    
    if ( !$found ){
      // Not found so revert to the original.
      $key = $string;
    }
    
    $args[0] = $this->_($key);
    return call_user_func_array('sprintf', $args);
  }
  
  /**
   * Passes a string thru a sprintf.
   * 
   * Note that this method can take a mixed number of arguments as for the sprintf function.
   * 
   * @param   string  $string  The base string.
   * @return   string  The translated string.
   */
  public function sprintf($string)
  {
    $args  = func_get_args();
    $count = count($args);
    
    $args[0] = $this->_($string);
    $args[0] = preg_replace('/\[\[%([0-9]+):[^\]]*\]\]/', '%\1$s', $args[0]);
    return call_user_func_array('sprintf', $args);
  }
  
  /**
   * Passes a string thru an printf.
   * 
   * Note that this method can take a mixed number of arguments as for the sprintf function.
   * 
   * @param   string  $string  The base string.
   * @return   string  The translated string.
   */
  public function printf($string)
  {
    $args  = func_get_args();
    $count = count($args);
    
    $args[0] = $this->_($string);
    return call_user_func_array('printf', $args);
  }
  
  /**
   * Determines is a key exists.
   *
   * @param   string  $string  The key to check.
   * @return   boolean  True, if the key exists.
   */
  public function hasKey($string)
  {
    $key = strtoupper($string);
    return isset($this->strings[$key]);
  }
  
  /**
   * Retrieve the list of key/value pairs starting with a given prefix
   * 
   * used in admin/items search for translated fields
   * 
   * @param   $prefix   The prefix to look for
   * @return   array key/value pairs where key is without prefix
   */
  public function getSome($prefix)
  {
    $strings=[];
    foreach($this->strings as $key => $value){
      if ( preg_match("/^".$prefix."(.+)$/", $key, $m) ){
        $strings[$m[1]] = $value;
      }
    }
    return $strings;
  }
  
  /**
   * Retrieve the list of key/value pairs filtered by regex
   * 
   * @param   string          $regex        The filter
   * @param   callable|null   $callback     The preg match callback function
   * @return   array           Key/value pairs
   */
  public function getByRegex($regex, $callback=null)
  {
    $strings=[];
    foreach($this->strings as $key => $value){
      if ( preg_match("/^".$regex."$/", $key, $m) ){
        $k = $callback === null ? $m[1] : $callback($m);
        $strings[$k] = $value;
      }
    }
    return $strings;
  }
  
  /**
   * Pluralize method
   *
   * This method processes a string to find plural
   *
   * @param   string  $string  The singular 
   * @return   string  The plural
   */
  public function pluralize($string)
  {
    return LanguageInflect::pluralize($string);
  }
  
  /**
   * Singularize method
   *
   * This method processes a string to find singular
   *
   * @param   string  $string  The plural 
   * @return   string  The singular
   */
  public function singularize($string)
  {
    return LanguageInflect::singularize($string);
  }
  
  /**
   * Pluralize a string according to a counter
   * 
   * @param   int     $count    The count 
   * @param   string  $string   The singular value
   * @return   string  The plural prefixed by the count
   */
  public function pluralize_if($count, $string)
  {
    return LanguageInflect::pluralize_if($count, $string);
  }
  
  /**
   * Check if string is plural
   * 
   * @param   string    $string     String to check
   * @return   bool      True if plural
   */
  public function isPlural($string)
  {
    return LanguageInflect::isPlural($string);
  }
  
  /**
   * Check if string is singular
   * 
   * @param   string    $string     String to check
   * @return   bool      True if singular
   */
  public function isSingular($string)
  {
    return LanguageInflect::isSingular($string);
  }
  
  /**
   * Transliterate function
   *
   * This method processes a string and replaces all accented UTF-8 characters by unaccented
   * ASCII-7 "equivalents".
   *
   * @param   string  $string  The string to transliterate.
   * @return   string  The transliteration of the string.
   */
  public function transliterate($string)
  {
    return LanguageTransliterate::transliterate($string);
  }
  
  /**
   * Returns an array of suffixes for plural rules.
   *
   * @param   int  $count  The count number the rule is for.
   * @return   array    The array of suffixes.
   */
  public function getPluralSuffixes($count)
  {
    return LanguageTransliterate::getPluralSuffixes($count);
  }

  /**
   * Returns an array of ignored search words
   * 
   * @return   array  The array of ignored search words.
   */
  public function getIgnoredSearchWords()
  {
    return LanguageTransliterate::getIgnoredSearchWords();
  }

  /**
   * Returns a lower limit int for length of search words
   * 
   * @return   int  The lower limit int for length of search words (3 if no value was set for a specific language).
   */
  public function getLowerLimitSearchWord()
  {
    return LanguageTransliterate::getLowerLimitSearchWord();
  }

  /**
   * Returns an upper limit int for length of search words
   * 
   * @return   int  The upper limit int for length of search words (20 if no value was set for a specific language).
   */
  public function getUpperLimitSearchWord()
  {
    return LanguageTransliterate::getUpperLimitSearchWord();
  }

  /**
   * Returns the number of characters displayed in search results.
   * 
   * @return   int  The number of characters displayed (200 if no value was set for a specific language).
   */
  public function getSearchDisplayedCharactersNumber()
  {
    return LanguageTransliterate::getSearchDisplayedCharactersNumber();
  }

  /**
   * Print all strings
   *
   * @return   void
   */
  public function debug()
  {
    ksort($this->strings);
    debugMe($this->strings)->end();
  }
}
