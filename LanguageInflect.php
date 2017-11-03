<?php 
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Language;

/**
 * Language inflector 
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com>
 */
class LanguageInflect
{
	/**
	 * Plural regexes
	 * 
	 * @var array
	 */
  protected static $plural = [
    '/(quiz)$/i'                      => "$1zes",
    '/^(ox)$/i'                       => "$1en",
    '/([m|l])ouse$/i'                 => "$1ice",
    '/(matr|vert|ind)ix|ex$/i'        => "$1ices",
    '/(x|ch|ss|sh)$/i'                => "$1es",
    '/([^aeiouy]|qu)y$/i'             => "$1ies",
    '/(hive)$/i'                      => "$1s",
    '/(?:([^f])fe|([lr])f)$/i'        => "$1$2ves",
    '/(shea|lea|loa|thie)f$/i'        => "$1ves",
    '/sis$/i'                         => "ses",
    '/([ti])um$/i'                    => "$1a",
    '/(tomat|potat|ech|her|vet)o$/i'  => "$1oes",
    '/(bu)s$/i'                       => "$1ses",
    '/(alias)$/i'                     => "$1es",
    '/(octop)us$/i'                   => "$1i",
    '/(ax|test)is$/i'                 => "$1es",
    '/(us)$/i'                        => "$1es",
    '/s$/'                            => "s",
    // '/(\D)$/'                         => "$1s",
    '/$/'                             => "s",
  ];
  
	/**
	 * Singular regexes
	 * 
	 * @var array
	 */
  protected static $singular = [
    '/(quiz)zes$/i'                   => "$1",
    '/(matr)ices$/i'                  => "$1ix",
    '/(vert|ind)ices$/i'              => "$1ex",
    '/^(ox)en$/i'                     => "$1",
    '/(alias)es$/i'                   => "$1",
    '/(octop|vir)i$/i'                => "$1us",
    '/(cris|ax|test)es$/i'            => "$1is",
    '/(shoe)s$/i'                     => "$1",
    '/(o)es$/i'                       => "$1",
    '/(bus)es$/i'                     => "$1",
    '/([m|l])ice$/i'                  => "$1ouse",
    '/(x|ch|ss|sh)es$/i'              => "$1",
    '/(m)ovies$/i'                    => "$1ovie",
    '/(s)eries$/i'                    => "$1eries",
    '/([^aeiouy]|qu)ies$/i'           => "$1y",
    '/([lr])ves$/i'                   => "$1f",
    '/(tive)s$/i'                     => "$1",
    '/(hive)s$/i'                     => "$1",
    '/(li|wi|kni)ves$/i'              => "$1fe",
    '/(shea|loa|lea|thie)ves$/i'      => "$1f",
    '/(^analy)ses$/i'                 => "$1sis",
    '/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i'  => "$1$2sis",        
    '/([ti])a$/i'                     => "$1um",
    '/(n)ews$/i'                      => "$1ews",
    '/(h|bl)ouses$/i'                 => "$1ouse",
    '/(corpse)s$/i'                   => "$1",
    '/(us)es$/i'                      => "$1",
    '/s$/'                            => "",
  ];   
  
	/**
	 * Irregular text strings
	 * 
	 * @var array
	 */
  protected static $irregular = [
    'move'      => 'moves',
    'foot'      => 'feet',
    'goose'     => 'geese',
    'sex'       => 'sexes',
    'child'     => 'children',
    'man'       => 'men',
    'tooth'     => 'teeth',
    'person'    => 'people',
  ];
  
	/**
	 * Uncountable text strings
	 * 
	 * @var array
	 */
  protected static $uncountable = [
    'sheep', 
    'fish',
    'deer',
    'series',
    'species',
    'money',
    'rice',
    'information',
    'equipment'
  ];
  
  /**
   * Add plural regexes
   * 
   * @param 	array   $regexes  Plural regexes to add to the existing set
   * @return 	void
   */
  public static function addPlural(array $regexes=[]) 
  {
    self::$plural = array_merge(self::$plural, $regexes);
  }
  
  /**
   * Add singular regexes
   * 
   * @param 	array   $regexes  Singular regexes to add to the existing set
   * @return 	void
   */
  public static function addSingular(array $regexes=[]) 
  {
    self::$singular = array_merge(self::$singular, $regexes);
  }
  
  /**
   * Add irregular strings
   * 
   * @param 	array $strings Irregular text strings to add to the existing set
   * @return 	void
   */
  public static function addIrregular(array $strings=[]) 
  {
    self::$irregular = array_merge(self::$irregular, $strings);
  }
  
  /**
   * Add uncountable strings
   * 
   * @param 	array   $strings  Uncountable text strings to add to the existing set
   * @return 	void
   */
  public static function addUncountable(array $strings=[]) 
  {
    self::$uncountable = array_merge(self::$uncountable, $strings);
  }
  
  /**
   * Pluralize a string
   * 
   * @param 	string  $string   Singular string
   * @return 	string
   */
  public static function pluralize($string) 
  {
    // save some time in the case that singular and plural are the same
    if ( in_array(strtolower($string), static::$uncountable) ){
      return $string;
    }
    
    // check for irregular singular forms
    foreach(static::$irregular as $pattern => $result){
      $pattern = '/'.$pattern.'$/i';
      
      if ( preg_match($pattern, $string) ){
        return preg_replace($pattern, $result, $string);
      }
    }
    
    // check for matches using regular expressions
    foreach(static::$plural as $pattern => $result){
      if ( preg_match($pattern, $string ) ){
        return preg_replace( $pattern, $result, $string );
      }
    }
    
    return $string;
  }
  
  /**
   * Singularize a string
   * 
   * @param 	string  $string   Plural string
   * @return 	string
   */
  public static function singularize($string)
  {
    // save some time in the case that singular and plural are the same
    if ( in_array(strtolower($string), static::$uncountable) ){
      return $string;
    }
    
    // check for irregular plural forms
    foreach(static::$irregular as $result => $pattern){
      $pattern = '/'.$pattern.'$/i';
      
      if ( preg_match($pattern, $string) ){
        return preg_replace($pattern, $result, $string);
      }
    }
    
    // check for matches using regular expressions
    foreach(static::$singular as $pattern => $result){
      if ( preg_match($pattern, $string) ){
        return preg_replace($pattern, $result, $string);
      }
    }
    
    return $string;
  }
  
  /**
   * Pluralize a string according to a counter
   * 
   * @param 	int     $count    Counter
   * @param 	string  $string   Singular string
   * @return 	string
   */
  public static function pluralize_if($count, $string)
  {
    if ( $count == 1 ){
      return "1 $string";
    }
    
    return $count.' '.static::pluralize($string);
  }
  
  /**
   * Check if string is plural
   * 
   * @param 	string  $string   Plural string
   * @return 	bool    True if plural
   */
  public static function isPlural($string)
  {
    return ( static::pluralize($string) === $string );
  }
  
  /**
   * Check if string is plural
   * 
   * @param 	string  $string   Singular string
   * @return 	bool    True if singular
   */
  public static function isSingular($string)
  {
    return ( static::singularize($string) === $string );
  }
}