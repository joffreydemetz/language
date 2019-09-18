<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Language;

use JDZ\Filesystem\Path;
use JDZ\Filesystem\Folder;
use JDZ\Filesystem\File;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\YamlParseException;
use RuntimeException;
/**
 * Language Base Object
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class Language 
{
  /**
   * Translator instance
   * 
   * @var Translator
   */
  public $translator;
  
  /**
   * Language metadata
   * 
   * @var LanguageMetas
   */
  public $metadata;
  
  /**
   * Form Keys
   * 
   * @var int
   */
  public $groupedKeys = [];
  
  public static function create()
  {
    return new self();
  }
  
  public function init()
  {
    $this->translator = new Translator($this->metadata->getIso());
    $this->translator->addLoader('array', new ArrayLoader());
    return $this;
  }
  
  /**
   * Load the language metadatas
   *
   * @param  string  $path  The path to a file
   * @return $this
   */
  public function setMetas(string $path)
  {
    $this->metadata = LanguageMetas::create();
    
    $metas = $this->parseYmlFile($path);
    
    foreach($metas as $key => $value){
      $method = 'set'.ucfirst($key);
      if ( method_exists($this->metadata, $method) ){
        $this->metadata->{$method}($value);
      }
      else {
        throw new RuntimeException('Could not set '.$key.' in LanguageMetas object');
      }
    }
    
    return $this;
  }
  
  public function getMetadata()
  {
    return $this->metadata;
  }
  
  public function getTranslator()
  {
    return $this->translator;
  }
  
  public function addIrregular(string $path)
  {
    $irregulars = $this->parseYmlFile($path);
    LanguageInflect::addIrregular($irregulars);
    return $this;
  }
  
  /**
   * Appends strings from an INI file
   *
   * @param  string  $path  The path to a file
   * @return $this
   */
  public function addIni(string $path, $locale=null, $domain=null)
  {
    if ( null === $locale ){
      $locale = $this->metadata->getIso();
    }
    
    if ( $strings = $this->parseIniFile($path) ){
      $this->add($strings, $locale, $domain);
    }
    
    return $this;
  }

  /**
   * Appends strings from an YML file
   *
   * @param  string  $path  The path to a file
   * @return $this
   */
  public function addYml(string $path, $locale=null, $domain=null)
  {
    if ( null === $locale ){
      $locale = $this->metadata->getIso();
    }
    
    if ( $strings = $this->parseYmlFile($path) ){
      $this->add($strings, $locale, $domain);
    }
    
    return $this;
  }
  
  /**
   * Appends the strings to the existing strings
   *
   * @param  array  $strings  Key/value pairs of strings 
   * @return void
   */
  public function add(array $strings, $locale=null, $domain=null)
  {
    if ( null === $locale ){
      $locale = $this->metadata->getIso();
    }
    
    $this->storeGroupKeys($strings);
    $this->translator->addResource('array', $strings, $locale, $domain);
  }
  
  public function has($key, array $parameters=[], $domain=null, $locale=null)
  {
    $trad = $this->trans($key, $parameters, $domain, $locale);
    if ( $trad !== $key ){
      return true;
    }
    return false;
  }
  
  public function trans($key, array $parameters=[], $domain=null, $locale=null)
  {
    $string = $this->translator->trans($key, $parameters, $domain, $locale);
    
    $lang = $this;
    
    $string = preg_replace_callback("/[\*]{3,}([A-Z_]+)[\*]{3,}/", function($m) use($lang, $parameters, $domain, $locale){
      return $lang->trans($m[1], $parameters, $domain, $locale);
    }, $string);
    
    // if ( preg_match("/^[\*]{3,}([A-Z_]+)[\*]{3,}$/", $string, $m) ){
      // return $this->trans($m[1], $parameters, $domain, $locale);
    // }
    
    // if ( $string === $key ){
      // throw new \Exception($key);
      // debugMe($key);
    // }
    
    return $string;
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
  public function plural($string, $n=0, array $parameters=[], $domain=null, $locale=null)
  { 
    $string = $this->translator->transChoice($string, $n, $parameters, $domain, $locale);
    $string = str_replace(array('\\\\', '\t', '\n'), array("\\", "\t", "\n"), $string);
    return $string;
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
        // $key = strtoupper($key);
        $data[$key] = $this->trans($key);
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
  public function _($key, array $parameters=[])
  {
    if ( $key == '' ){
      return '';
    }
    
    $string = $this->trans($key, $parameters);
    $string = str_replace(array('\\\\', '\t', '\n'), array("\\", "\t", "\n"), $string);
    return $string;
  }
  
  public function getGroup(string $group, $domain=null, $locale=null)
  {
    $trads = [];
    if ( isset($this->groupedKeys[$group]) ){
      $this->groupedKeys[$group] = array_unique($this->groupedKeys[$group]);
      foreach($this->groupedKeys[$group] as $key => $value){
        $trads[$key] = $this->_($value, [], $domain, $locale);
      }
    }
    return $trads;
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
   * Extract translations from INI file
   *
   * @param  string  $path  The path of ini file
   * @return array   Key/Value pairs of translations
   */
  protected function parseIniFile($path)
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
    }
    
    return $strings;
  }
  
  /**
   * Extract translations from YML file
   *
   * @param  string  $path  The path of yml file
   * @return array   Key/Value pairs of translations
   */
  protected function parseYmlFile($path)
  {
    try {
      $strings = Yaml::parse( File::read($path) );
    } 
    catch(YamlParseException $e){
      $strings = [];
      throw new RuntimeException("Unable to parse the YAML string: %s", $e->getMessage());
    }
    
    return $strings;
  }
  
  protected function storeGroupKeys($strings)
  {
    foreach($strings as $key => $value){
      if ( preg_match("/^FILESYSTEM_(.+)$/", $key, $m) ){
        $this->groupedKeys['filesystem'][$m[1]] = $key;
      }
      elseif ( preg_match("/^DATE_((DAY|MONTH)_.+)$/", $key, $m) ){
        $this->groupedKeys['date'][$m[1]] = $key;
      }
      elseif ( preg_match("/^DATABASE_(.+)$/", $key, $m) ){
        $this->groupedKeys['database'][$m[1]] = $key;
      }
      elseif ( preg_match("/^((ERROR_FORM_FIELD|HELP_FIELD|FIELD|FIELDSET)_.+)$/", $key, $m) ){
        $this->groupedKeys['form'][$m[1]] = $key;
      }
    }
  }
}
