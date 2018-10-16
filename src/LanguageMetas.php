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
class LanguageMetas
{
  /**
   * ISO-Alpha-2 code
   * 
   * @var string
   */
  public $iso;
  
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
  public $rtl = false;
  
  /**
   * Language first day
   * 
   * @var int
   */
  public $firstDay = 1;
  
  /**
   * Language locale
   * 
   * @var array
   */
  public $locale;
  
  public static function create()
  {
    return new self();
  }
  
  public function setIso(string $iso)
  {
    $this->iso = $iso;
    return $this;
  }
  
  public function setTag(bool $tag)
  {
    $this->tag = $tag;
    return $this;
  }
  
  public function setCode(string $code)
  {
    $this->code = $code;
    return $this;
  }
  
  public function setName(string $name)
  {
    $this->name = $name;
    return $this;
  }
  
  public function setRtl(bool $rtl)
  {
    $this->rtl = $rtl;
    return $this;
  }
  
  public function setFirstDay(int $firstDay)
  {
    $this->firstDay = $firstDay;
    return $this;
  }
  
  public function setLocale(array $locale)
  {
    $this->locale = $locale;
    return $this;
  }
  
  public function getIso()
  {
    return $this->iso;
  }
  
  public function getTag()
  {
    return $this->tag;
  }
  
  public function getCode()
  {
    return $this->code;
  }
  
  public function getName()
  {
    return $this->name;
  }
  
  public function getRtl()
  {
    return $this->rtl;
  }
  
  public function getFirstDay()
  {
    return $this->firstDay;
  }
  
  public function getLocale()
  {
    return $this->locale;
  }
}