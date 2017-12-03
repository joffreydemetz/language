<?php
/**
 * (c) Joffrey Demetz <joffrey.demetz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace JDZ\Language;

/**
 * Language transliterate 
 * 
 * @author Joffrey Demetz <joffrey.demetz@gmail.com> */
class LanguageTransliterate 
{
  /**
   * Returns an array of suffixes for plural rules.
   *
   * @param   integer  $count  The count number the rule is for.
   * @return   array    The array of suffixes.
   */
  public static function getPluralSuffixes($count)
  {
    if ($count == 0) {
      $return =  ['0'];
    }
    elseif($count == 1) {
      $return =  ['1'];
    }
    else {
      $return = ['MORE'];
    }
    return $return;
  }
  
  /**
   * Returns an array of ignored search words
   * 
   * @return   array  The array of ignored search words.
   */
  public static function getIgnoredSearchWords()
  {
    $search_ignore = [];
    $search_ignore[] = "et";
    $search_ignore[] = "si";
    $search_ignore[] = "ou";
    return $search_ignore;
  }
  
  /**
   * Returns a lower limit integer for length of search words
   * 
   * @return   integer  The lower limit integer for length of search words (3 if no value was set for a specific language).
   */
  public static function getLowerLimitSearchWord()
  {
    return 3;
  }
  
  /**
   * Returns an upper limit integer for length of search words
   * 
   * @return   integer  The upper limit integer for length of search words (20 if no value was set for a specific language).
   */
  public static function getUpperLimitSearchWord()
  {
    return 20;
  }
  
  /**
   * Returns the number of characters displayed in search results.
   * 
   * @return   integer  The number of characters displayed (200 if no value was set for a specific language).
   */
  public static function getSearchDisplayedCharactersNumber()
  {
    return 200;
  }
  
  /**
   * Translilterate string.
   * 
   * @param   string    $string   The string to transliterate.
   * @return   string    The transliterated tring.
   */
  public static function transliterate($string)
  {
    $str = mb_strtolower($string);

    $glyph_array = [
      'a'    =>  'a,à,á,â,ã,ä,å,ā,ă,ą,ḁ,α,ά',
      'ae'  =>  'æ',
      'b'    =>  'β,б',
      'c'    =>  'c,ç,ć,ĉ,ċ,č,ћ,ц',
      'ch'  =>  'ч',
      'd'    =>  'ď,đ,Ð,д,ђ,δ,ð',
      'dz'  =>  'џ',
      'e'    =>  'e,è,é,ê,ë,ē,ĕ,ė,ę,ě,э,ε,έ',
      'f'    =>  'ƒ,ф',
      'g'    =>  'ğ,ĝ,ğ,ġ,ģ,г,γ',
      'h'    =>  'ĥ,ħ,Ħ,х',
      'i'    =>  'i,ì,í,î,ï,ı,ĩ,ī,ĭ,į,и,й,ъ,ы,ь,η,ή',
      'ij'  =>  'ĳ',
      'j'    =>  'ĵ,j',
      'ja'  =>  'я',
      'ju'  =>  'яю',
      'k'    =>  'ķ,ĸ,κ',
      'l'    =>  'ĺ,ļ,ľ,ŀ,ł,л,λ',
      'lj'  =>  'љ',
      'm'    =>  'μ,м',
      'n'    =>  'ñ,ņ,ň,ŉ,ŋ,н,ν',
      'nj'  =>  'њ',
      'o'    =>  'ò,ó,ô,õ,ø,ō,ŏ,ő,ο,ό,ω,ώ',
      'oe'  =>  'œ,ö',
      'p'    =>  'п,π',
      'ph'  =>  'φ',
      'ps'  =>  'ψ',
      'r'    =>  'ŕ,ŗ,ř,р,ρ,σ,ς',
      's'    =>  'ş,ś,ŝ,ş,š,с',
      'ss'  =>  'ß,ſ',
      'sh'  =>  'ш',
      'shch'  =>  'щ',
      't'    =>  'ţ,ť,ŧ,τ,т',
      'th'  =>  'θ',
      'u'    =>  'u,ù,ú,û,ü,ũ,ū,ŭ,ů,ű,ų,у',
      'v'    =>  'в',
      'w'    =>  'ŵ',
      'x'    =>  'χ,ξ',
      'y'    =>  'ý,þ,ÿ,ŷ',
      'z'    =>  'ź,ż,ž,з,ж,ζ'
    ];
    
    foreach($glyph_array as $letter => $glyphs){
      $glyphs = explode(',', $glyphs);
      $str    = str_replace($glyphs, $letter, $str);
    }

    return $str;
  }
  
  /**
   * Translilterate string.
   * 
   * @param   string    $string   The string to transliterate.
   * @param   int       $case     <=0 for lowercase / >=0 for uppercase.
   * @return   string    The transliterated tring.
   */
  public static function utf8_latin_to_ascii($string, $case=0)
  {
    static $UTF8_LOWER_ACCENTS;
    static $UTF8_UPPER_ACCENTS;

    if ( $case <= 0 ){
      if ( !isset($UTF8_LOWER_ACCENTS) ){
        $UTF8_LOWER_ACCENTS = [
          'à' => 'a',
          'ô' => 'o',
          'ď' => 'd',
          'ḟ' => 'f',
          'ë' => 'e',
          'š' => 's',
          'ơ' => 'o',
          'ß' => 'ss',
          'ă' => 'a',
          'ř' => 'r',
          'ț' => 't',
          'ň' => 'n',
          'ā' => 'a',
          'ķ' => 'k',
          'ŝ' => 's',
          'ỳ' => 'y',
          'ņ' => 'n',
          'ĺ' => 'l',
          'ħ' => 'h',
          'ṗ' => 'p',
          'ó' => 'o',
          'ú' => 'u',
          'ě' => 'e',
          'é' => 'e',
          'ç' => 'c',
          'ẁ' => 'w',
          'ċ' => 'c',
          'õ' => 'o',
          'ṡ' => 's',
          'ø' => 'o',
          'ģ' => 'g',
          'ŧ' => 't',
          'ș' => 's',
          'ė' => 'e',
          'ĉ' => 'c',
          'ś' => 's',
          'î' => 'i',
          'ű' => 'u',
          'ć' => 'c',
          'ę' => 'e',
          'ŵ' => 'w',
          'ṫ' => 't',
          'ū' => 'u',
          'č' => 'c',
          'ö' => 'oe',
          'è' => 'e',
          'ŷ' => 'y',
          'ą' => 'a',
          'ł' => 'l',
          'ų' => 'u',
          'ů' => 'u',
          'ş' => 's',
          'ğ' => 'g',
          'ļ' => 'l',
          'ƒ' => 'f',
          'ž' => 'z',
          'ẃ' => 'w',
          'ḃ' => 'b',
          'å' => 'a',
          'ì' => 'i',
          'ï' => 'i',
          'ḋ' => 'd',
          'ť' => 't',
          'ŗ' => 'r',
          'ä' => 'ae',
          'í' => 'i',
          'ŕ' => 'r',
          'ê' => 'e',
          'ü' => 'ue',
          'ò' => 'o',
          'ē' => 'e',
          'ñ' => 'n',
          'ń' => 'n',
          'ĥ' => 'h',
          'ĝ' => 'g',
          'đ' => 'd',
          'ĵ' => 'j',
          'ÿ' => 'y',
          'ũ' => 'u',
          'ŭ' => 'u',
          'ư' => 'u',
          'ţ' => 't',
          'ý' => 'y',
          'ő' => 'o',
          'â' => 'a',
          'ľ' => 'l',
          'ẅ' => 'w',
          'ż' => 'z',
          'ī' => 'i',
          'ã' => 'a',
          'ġ' => 'g',
          'ṁ' => 'm',
          'ō' => 'o',
          'ĩ' => 'i',
          'ù' => 'u',
          'į' => 'i',
          'ź' => 'z',
          'á' => 'a',
          'û' => 'u',
          'þ' => 'th',
          'ð' => 'dh',
          'æ' => 'ae',
          'µ' => 'u',
          'ĕ' => 'e',
          'œ' => 'oe'
        ];
      }

      $string = str_replace(array_keys($UTF8_LOWER_ACCENTS), array_values($UTF8_LOWER_ACCENTS), $string);
    }

    if ( $case >= 0 ){
      if ( !isset($UTF8_UPPER_ACCENTS) ){
        $UTF8_UPPER_ACCENTS = [
          'À' => 'A',
          'Ô' => 'O',
          'Ď' => 'D',
          'Ḟ' => 'F',
          'Ë' => 'E',
          'Š' => 'S',
          'Ơ' => 'O',
          'Ă' => 'A',
          'Ř' => 'R',
          'Ț' => 'T',
          'Ň' => 'N',
          'Ā' => 'A',
          'Ķ' => 'K',
          'Ŝ' => 'S',
          'Ỳ' => 'Y',
          'Ņ' => 'N',
          'Ĺ' => 'L',
          'Ħ' => 'H',
          'Ṗ' => 'P',
          'Ó' => 'O',
          'Ú' => 'U',
          'Ě' => 'E',
          'É' => 'E',
          'Ç' => 'C',
          'Ẁ' => 'W',
          'Ċ' => 'C',
          'Õ' => 'O',
          'Ṡ' => 'S',
          'Ø' => 'O',
          'Ģ' => 'G',
          'Ŧ' => 'T',
          'Ș' => 'S',
          'Ė' => 'E',
          'Ĉ' => 'C',
          'Ś' => 'S',
          'Î' => 'I',
          'Ű' => 'U',
          'Ć' => 'C',
          'Ę' => 'E',
          'Ŵ' => 'W',
          'Ṫ' => 'T',
          'Ū' => 'U',
          'Č' => 'C',
          'Ö' => 'Oe',
          'È' => 'E',
          'Ŷ' => 'Y',
          'Ą' => 'A',
          'Ł' => 'L',
          'Ų' => 'U',
          'Ů' => 'U',
          'Ş' => 'S',
          'Ğ' => 'G',
          'Ļ' => 'L',
          'Ƒ' => 'F',
          'Ž' => 'Z',
          'Ẃ' => 'W',
          'Ḃ' => 'B',
          'Å' => 'A',
          'Ì' => 'I',
          'Ï' => 'I',
          'Ḋ' => 'D',
          'Ť' => 'T',
          'Ŗ' => 'R',
          'Ä' => 'Ae',
          'Í' => 'I',
          'Ŕ' => 'R',
          'Ê' => 'E',
          'Ü' => 'Ue',
          'Ò' => 'O',
          'Ē' => 'E',
          'Ñ' => 'N',
          'Ń' => 'N',
          'Ĥ' => 'H',
          'Ĝ' => 'G',
          'Đ' => 'D',
          'Ĵ' => 'J',
          'Ÿ' => 'Y',
          'Ũ' => 'U',
          'Ŭ' => 'U',
          'Ư' => 'U',
          'Ţ' => 'T',
          'Ý' => 'Y',
          'Ő' => 'O',
          'Â' => 'A',
          'Ľ' => 'L',
          'Ẅ' => 'W',
          'Ż' => 'Z',
          'Ī' => 'I',
          'Ã' => 'A',
          'Ġ' => 'G',
          'Ṁ' => 'M',
          'Ō' => 'O',
          'Ĩ' => 'I',
          'Ù' => 'U',
          'Į' => 'I',
          'Ź' => 'Z',
          'Á' => 'A',
          'Û' => 'U',
          'Þ' => 'Th',
          'Ð' => 'Dh',
          'Æ' => 'Ae',
          'Ĕ' => 'E',
          'Œ' => 'Oe'
        ];
      }
      
      $string = str_replace(array_keys($UTF8_UPPER_ACCENTS), array_values($UTF8_UPPER_ACCENTS), $string);
    }

    return $string;
  }
}
