<?php

/**
 * sfCultureInfo class file.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the BSD License.
 *
 * Copyright(c) 2004 by Qiang Xue. All rights reserved.
 *
 * To contact the author write to {@link mailto:qiang.xue@gmail.com Qiang Xue}
 * The latest version of PRADO can be obtained from:
 * {@link http://prado.sourceforge.net/}
 *
 * @author     Wei Zhuo <weizhuo[at]gmail[dot]com>
 * @version    $Id: sfCultureInfo.class.php 32741 2011-07-09 09:41:59Z fabien $
 * @package    symfony
 * @subpackage i18n
 */

/**
 * sfCultureInfo class.
 *
 * Represents information about a specific culture including the
 * names of the culture, the calendar used, as well as access to
 * culture-specific objects that provide methods for common operations,
 * such as formatting dates, numbers, and currency.
 *
 * The sfCultureInfo class holds culture-specific information, such as the
 * associated language, sublanguage, country/region, calendar, and cultural
 * conventions. This class also provides access to culture-specific
 * instances of sfDateTimeFormatInfo and sfNumberFormatInfo. These objects
 * contain the information required for culture-specific operations,
 * such as formatting dates, numbers and currency.
 *
 * The culture names follow the format "<languagecode>_<country/regioncode>",
 * where <languagecode> is a lowercase two-letter code derived from ISO 639
 * codes. You can find a full list of the ISO-639 codes at
 * http://www.ics.uci.edu/pub/ietf/http/related/iso639.txt
 *
 * The <country/regioncode2> is an uppercase two-letter code derived from
 * ISO 3166. A copy of ISO-3166 can be found at
 * http://www.chemie.fu-berlin.de/diverse/doc/ISO_3166.html
 *
 * For example, Australian English is "en_AU".
 *
 * @author Xiang Wei Zhuo <weizhuo[at]gmail[dot]com>
 * @version v1.0, last update on Sat Dec 04 13:41:46 EST 2004
 * @package    symfony
 * @subpackage i18n
 */
class sfCultureInfo
{
  /**
   * ICU data filename extension.
   * @var string
   */
  protected $dataFileExt = '.dat';

  /**
   * The ICU data array.
   * @var array
   */
  protected $data = array();

  /**
   * The current culture.
   * @var string
   */
  protected $culture;

  /**
   * Directory where the ICU data is stored.
   * @var string
   */
  protected $dataDir;

  /**
   * A list of ICU date files loaded.
   * @var array
   */
  protected $dataFiles = array();

  /**
   * The current date time format info.
   * @var sfDateTimeFormatInfo
   */
  protected $dateTimeFormat;

  /**
   * The current number format info.
   * @var sfNumberFormatInfo
   */
  protected $numberFormat;
  
  /**
   * A list of properties that are accessable/writable.
   * @var array
   */ 
  protected $properties = array();

  /**
   * Culture type, all.
   * @see getCultures()
   * @var int
   */
  const ALL = 0;

  /**
   * Culture type, neutral.
   * @see getCultures()
   * @var int
   */
  const NEUTRAL = 1;

  /**
   * Culture type, specific.
   *
   * @see getCultures()
   * @var int
   */
  const SPECIFIC = 2;

  /**
   * Gets the sfCultureInfo that for this culture string.
   *
   * @param string  $culture The culture for this instance
   * @return sfCultureInfo Invariant culture info is "en"
   */
  public static function getInstance($culture = 'en')
  {
    static $instances = array();

    if (!isset($instances[$culture]))
    {
      $instances[$culture] = new sfCultureInfo($culture);
    }

    return $instances[$culture];
  }

  /**
   * Displays the culture name.
   *
   * @return string the culture name.
   * @see getName()
   */
  public function __toString()
  {
    return $this->getName();
  }

  /**
   * Allows functions that begins with 'set' to be called directly
   * as an attribute/property to retrieve the value.
   *
   * @param string $name The property to get
   * @return mixed
   */
  public function __get($name)
  {
    $getProperty = 'get'.$name;
    if (in_array($getProperty, $this->properties))
    {
      return $this->$getProperty();
    }
    else
    {
      throw new sfException(sprintf('Property %s does not exists.', $name));
    }
  }

  /**
   * Allows functions that begins with 'set' to be called directly
   * as an attribute/property to set the value.
   *
   * @param string $name  The property to set
   * @param string $value The property value
   */
  public function __set($name, $value)
  {
    $setProperty = 'set'.$name;
    if (in_array($setProperty, $this->properties))
    {
      $this->$setProperty($value);
    }
    else
    {
      throw new sfException(sprintf('Property %s can not be set.', $name));
    }
  }

  /**
   * Initializes a new instance of the sfCultureInfo class based on the 
   * culture specified by name. E.g. <code>new sfCultureInfo('en_AU');</code>
   * The culture indentifier must be of the form 
   * "<language>_(country/region/variant)".
   *
   * @param string $culture a culture name, e.g. "en_AU".
   * @return return new sfCultureInfo.
   */
  public function __construct($culture = 'en')
  {
    $this->properties = get_class_methods($this);

    if (empty($culture))
    {
      $culture = 'en';
    }

    $this->dataDir = self::dataDir();
    $this->dataFileExt = self::fileExt();

    $this->setCulture($culture);

    $this->loadCultureData('root');
    $this->loadCultureData($culture);
  }

  /**
   * Gets the default directory for the ICU data.
   * The default is the "data" directory for this class.
   *
   * @return string directory containing the ICU data.
   */
  protected static function dataDir()
  {
    return dirname(__FILE__).'/data/';
  }

  /**
   * Gets the filename extension for ICU data. Default is ".dat".
   *
   * @return string filename extension for ICU data.
   */
  protected static function fileExt()
  {
    return '.dat';
  }

  /**
   * Determines if a given culture is valid. Simply checks that the
   * culture data exists.
   *
   * @param string $culture a culture
   * @return boolean true if valid, false otherwise.
   */
  static public function validCulture($culture)
  {
    if (preg_match('/^[a-z]{2}(_[A-Z]{2,5}){0,2}$/', $culture))
    {
      return is_file(self::dataDir().$culture.self::fileExt());
    }

    return false;
  }

  /**
   * Sets the culture for the current instance. The culture indentifier
   * must be of the form "<language>_(country/region)".
   *
   * @param string $culture culture identifier, e.g. "fr_FR_EURO".
   */
  protected function setCulture($culture)
  {
    if (!empty($culture))
    {
      if (!preg_match('/^[a-z]{2}(_[A-Z]{2,5}){0,2}$/', $culture))
      {
        throw new sfException(sprintf('Invalid culture supplied: %s', $culture));
      }
    }

    $this->culture = $culture;
  }

  /**
   * Loads the ICU culture data for the specific culture identifier.
   *
   * @param string $culture the culture identifier.
   */
  protected function loadCultureData($culture)
  {
    $file_parts = explode('_', $culture);
    $current_part = $file_parts[0];

    $files = array($current_part);

    for ($i = 1, $max = count($file_parts); $i < $max; $i++)
    {
      $current_part .= '_'.$file_parts[$i];
      $files[] = $current_part;
    }

    foreach ($files as $file)
    {
      $filename = $this->dataDir.$file.$this->dataFileExt;

      if (is_file($filename) == false)
      {
        throw new sfException(sprintf('Data file for "%s" was not found.', $file));
      }

      if (in_array($filename, $this->dataFiles) == false)
      {
        array_unshift($this->dataFiles, $file);

        $data = &$this->getData($filename);
        $this->data[$file] = &$data;

        if (isset($data['__ALIAS']))
        {
          $this->loadCultureData($data['__ALIAS']);
        }
        unset($data);
      }
    }
  }

  /**
   * Gets the data by unserializing the ICU data from disk.
   * The data files are cached in a static variable inside
   * this function.
   *
   * @param string $filename the ICU data filename
   * @return array ICU data 
   */
  protected function &getData($filename)
  {
    static $data  = array();
    static $files = array();

    if (!in_array($filename, $files))
    {
      $data[$filename] = unserialize(file_get_contents($filename));
      $files[] = $filename;
    }

    return $data[$filename];
  }

  /**
   * Finds the specific ICU data information from the data.
   * The path to the specific ICU data is separated with a slash "/".
   * E.g. To find the default calendar used by the culture, the path
   * "calendar/default" will return the corresponding default calendar.
   * Use merge=true to return the ICU including the parent culture.
   * E.g. The currency data for a variant, say "en_AU" contains one
   * entry, the currency for AUD, the other currency data are stored
   * in the "en" data file. Thus to retrieve all the data regarding 
   * currency for "en_AU", you need to use findInfo("Currencies,true);.
   *
   * @param string  $path   the data you want to find.
   * @param boolean $merge  merge the data from its parents.
   * @return mixed the specific ICU data.
   */
  protected function findInfo($path = '/', $merge = false)
  {
    $result = array();
    foreach ($this->dataFiles as $section)
    {
      $info = $this->searchArray($this->data[$section], $path);

      if ($info)
      {
        if ($merge)
        {
          $result = $this->array_add($result, $info);
        }
        else
        {
          return $info;
        }
      }
    }

    return $result;
  }

  /**
   * Adds an array to an already existing array.
   * If an element is already existing in array1 it is not overwritten.
   * If this element is an array this logic will be applied recursively.
   */
  private function array_add($array1, $array2)
  {
    foreach ($array2 as $key => $value)
    {
      if (isset($array1[$key]))
      {
        if(is_array($array1[$key]) && is_array($value))
        {
          $array1[$key] = $this->array_add($array1[$key], $value);
        }
      }
      else
      {
        $array1[$key] = $value;
      }
    }
    return $array1;
  }

  /**
   * Searches the array for a specific value using a path separated using
   * slash "/" separated path. e.g to find $info['hello']['world'],
   * the path "hello/world" will return the corresponding value.
   *
   * @param array   $info  the array for search
   * @param string  $path  slash "/" separated array path.
   * @return mixed the value array using the path
   */
  protected function searchArray($info, $path = '/')
  {
    $index = explode('/', $path);

    $array = $info;

    for ($i = 0, $max = count($index); $i < $max; $i++)
    {
      $k = $index[$i];
      if ($i < $max - 1 && isset($array[$k]))
      {
        $array = $array[$k];
      }
      else if ($i == $max - 1 && isset($array[$k]))
      {
        return $array[$k];
      }
    }
  }
  
  /**
   * Gets the culture name in the format 
   * "<languagecode2>_(country/regioncode2)".
   *
   * @return string culture name.
   */
  public function getName()
  {
    return $this->culture;
  }

  /**
   * Gets the sfDateTimeFormatInfo that defines the culturally appropriate
   * format of displaying dates and times.
   *
   * @return sfDateTimeFormatInfo date time format information for the culture.
   */
  public function getDateTimeFormat()
  {
    if (null === $this->dateTimeFormat)
    {
      $calendar = $this->getCalendar();
      $info = $this->findInfo("calendar/{$calendar}", true);
      $this->setDateTimeFormat(new sfDateTimeFormatInfo($info));
    }

    return $this->dateTimeFormat;
  }

  /**
   * Sets the date time format information.
   *
   * @param sfDateTimeFormatInfo $dateTimeFormat the new date time format info.
   */
  public function setDateTimeFormat($dateTimeFormat)
  {
    $this->dateTimeFormat = $dateTimeFormat;
  }

  /**
   * Gets the default calendar used by the culture, e.g. "gregorian".
   *
   * @return string the default calendar.
   */
  public function getCalendar()
  {
    return $this->findInfo('calendar/default');
  }

  /**
   * Gets the culture name in the language that the culture is set
   * to display. Returns <code>array('Language','Country');</code>
   * 'Country' is omitted if the culture is neutral.
   *
   * @return array array with language and country as elements, localized.
   */
  public function getNativeName()
  {
    $lang = substr($this->culture, 0, 2);
    $reg = substr($this->culture, 3, 2);
    $language = $this->findInfo("Languages/{$lang}");
    $region = $this->findInfo("Countries/{$reg}");
    if ($region)
    {
      return $language.' ('.$region.')';
    }
    else
    {
      return $language;
    }
  }

  /**
   * Gets the culture name in English.
   * Returns <code>array('Language','Country');</code>
   * 'Country' is omitted if the culture is neutral.
   *
   * @return array array with language and country as elements.
   */
  public function getEnglishName()
  {
    $lang = substr($this->culture, 0, 2);
    $reg = substr($this->culture, 3, 2);
    $culture = $this->getInvariantCulture();

    $language = $culture->findInfo("Languages/{$lang}");
    if (count($language) == 0)
    {
      return $this->culture;
    }

    $region = $culture->findInfo("Countries/{$reg}");

    return $region ? $language.' ('.$region.')' : $language;
  }

  /**
   * Gets the sfCultureInfo that is culture-independent (invariant).
   * Any changes to the invariant culture affects all other
   * instances of the invariant culture.
   * The invariant culture is assumed to be "en";
   *
   * @return sfCultureInfo invariant culture info is "en".
   */
  static function getInvariantCulture()
  {
    static $invariant;

    if (null === $invariant)
    {
      $invariant = new sfCultureInfo();
    }

    return $invariant;
  }

  /**
   * Gets a value indicating whether the current sfCultureInfo 
   * represents a neutral culture. Returns true if the culture
   * only contains two characters.
   *
   * @return boolean true if culture is neutral, false otherwise.
   */
  public function getIsNeutralCulture()
  {
    return strlen($this->culture) == 2;
  }

  /**
   * Gets the sfNumberFormatInfo that defines the culturally appropriate
   * format of displaying numbers, currency, and percentage.
   *
   * @return sfNumberFormatInfo the number format info for current culture.
   */
  public function getNumberFormat()
  {
    if (null === $this->numberFormat)
    {
      $elements = $this->findInfo('NumberElements');
      $patterns = $this->findInfo('NumberPatterns');
      $currencies = $this->getCurrencies(null, true);
      $data = array('NumberElements' => $elements, 'NumberPatterns' => $patterns, 'Currencies' => $currencies);

      $this->setNumberFormat(new sfNumberFormatInfo($data));
    }

    return $this->numberFormat;
  }

  /**
   * Sets the number format information.
   *
   * @param sfNumberFormatInfo $numberFormat the new number format info.
   */
  public function setNumberFormat($numberFormat)
  {
    $this->numberFormat = $numberFormat;
  }

  /**
   * Gets the sfCultureInfo that represents the parent culture of the 
   * current sfCultureInfo
   *
   * @return sfCultureInfo parent culture information.
   */
  public function getParent()
  {
    if (strlen($this->culture) == 2)
    {
      return $this->getInvariantCulture();
    }

    return new sfCultureInfo(substr($this->culture, 0, 2));
  }

  /**
   * Gets the list of supported cultures filtered by the specified 
   * culture type. This is an EXPENSIVE function, it needs to traverse
   * a list of ICU files in the data directory.
   * This function can be called statically.
   *
   * @param int $type culture type, sfCultureInfo::ALL, sfCultureInfo::NEUTRAL
   * or sfCultureInfo::SPECIFIC.
   * @return array list of culture information available. 
   */
  static function getCultures($type = sfCultureInfo::ALL)
  {
    $dataDir = sfCultureInfo::dataDir();
    $dataExt = sfCultureInfo::fileExt();
    $dir = dir($dataDir);

    $neutral = array();
    $specific = array();

    while (false !== ($entry = $dir->read()))
    {
      if (is_file($dataDir.$entry) && substr($entry, -4) == $dataExt && $entry != 'root'.$dataExt)
      {
        $culture = substr($entry, 0, -4);
        if (strlen($culture) == 2)
        {
          $neutral[] = $culture;
        }
        else
        {
          $specific[] = $culture;
        }
      }
    }
    $dir->close();

    switch ($type)
    {
      case sfCultureInfo::ALL:
        $all =  array_merge($neutral, $specific);
        sort($all);
        return $all;
        break;
      case sfCultureInfo::NEUTRAL:
        return $neutral;
        break;
      case sfCultureInfo::SPECIFIC:
        return $specific;
        break;
    }
  }

  /**
   * Get the country name in the current culture for the given code.
   *
   * @param  string $code A valid country code
   *
   * @return string The country name in the current culture
   */
  public function getCountry($code)
  {
    $countries = $this->findInfo('Countries', true);

    if (!isset($countries[$code]))
    {
      throw new InvalidArgumentException(sprintf('The country %s does not exist.', $code));
    }

    return $countries[$code];
  }

  /**
   * Get the currency name in the current culture for the given code.
   *
   * @param  string $code A valid currency code
   *
   * @return string The currency name in the current culture
   */
  public function getCurrency($code)
  {
    $currencies = $this->findInfo('Currencies', true);

    if (!isset($currencies[$code]))
    {
      throw new InvalidArgumentException(sprintf('The currency %s does not exist.', $code));
    }

    return $currencies[$code][1];
  }

  /**
   * Get the language name in the current culture for the given code.
   *
   * @param  string $code A valid language code
   *
   * @return string The language name in the current culture
   */
  public function getLanguage($code)
  {
    $languages = $this->findInfo('Languages', true);

    if (!isset($languages[$code]))
    {
      throw new InvalidArgumentException(sprintf('The language %s does not exist.', $code));
    }

    return $languages[$code];
  }

  /**
   * Gets a list of countries in the language of the localized version.
   *
   * @param  array $countries An array of countries used to restrict the returned array (null by default, which means all countries)
   *
   * @return array a list of localized country names. 
   */
  public function getCountries($countries = null)
  {
    // remove integer keys as they do not represent countries
    $allCountries = array();
    foreach ($this->findInfo('Countries', true) as $key => $value)
    {
      if (!is_int($key))
      {
        $allCountries[$key] = $value;
      }
    }

    // restrict countries to a sub-set
    if (null !== $countries)
    {
      if ($problems = array_diff($countries, array_keys($allCountries)))
      {
        throw new InvalidArgumentException(sprintf('The following countries do not exist: %s.', implode(', ', $problems)));
      }

      $allCountries = array_intersect_key($allCountries, array_flip($countries));
    }

    $this->sortArray($allCountries);

    return $allCountries;
  }

  /**
   * Gets a list of currencies in the language of the localized version.
   *
   * @param  array   $currencies An array of currencies used to restrict the returned array (null by default, which means all currencies)
   * @param  Boolean $full       Whether to return the symbol and the name or not (false by default)
   *
   * @return array a list of localized currencies.
   */
  public function getCurrencies($currencies = null, $full = false)
  {
    $allCurrencies = $this->findInfo('Currencies', true);

    // restrict countries to a sub-set
    if (null !== $currencies)
    {
      if ($problems = array_diff($currencies, array_keys($allCurrencies)))
      {
        throw new InvalidArgumentException(sprintf('The following currencies do not exist: %s.', implode(', ', $problems)));
      }

      $allCurrencies = array_intersect_key($allCurrencies, array_flip($currencies));
    }

    if (!$full)
    {
      foreach ($allCurrencies as $key => $value)
      {
        $allCurrencies[$key] = $value[1];
      }
    }

    $this->sortArray($allCurrencies);

    return $allCurrencies;
  }

  /**
   * Gets a list of languages in the language of the localized version.
   *
   * @param  array $languages An array of languages used to restrict the returned array (null by default, which means all languages)
   *
   * @return array list of localized language names.
   */
  public function getLanguages($languages = null)
  {
    $allLanguages = $this->findInfo('Languages', true);

    // restrict languages to a sub-set
    if (null !== $languages)
    {
      if ($problems = array_diff($languages, array_keys($allLanguages)))
      {
        throw new InvalidArgumentException(sprintf('The following languages do not exist: %s.', implode(', ', $problems)));
      }

      $allLanguages = array_intersect_key($allLanguages, array_flip($languages));
    }

    $this->sortArray($allLanguages);

    return $allLanguages;
  }

  /**
   * Gets a list of scripts in the language of the localized version.
   *
   * @return array list of localized script names.
   */
  public function getScripts()
  {
    return $this->findInfo('Scripts', true);
  }

  /**
   * Gets a list of timezones in the language of the localized version.
   *
   * @return array list of localized timezones.
   */
  public function getTimeZones()
  {
    //new format since ICU 3.8
    //zoneStrings contains metaTimezones
    $metadata = $this->findInfo('zoneStrings', true);
    //TimeZones contains the Timezone name => metaTimezone identifier
    $timeZones = $this->findInfo('TimeZones', true);
    foreach ($timeZones as $key => $value)
    {
      $timeZones[$key] = $metadata['meta:'.$value];
      $timeZones[$key]['identifier'] = $key;
      $timeZones[$key]['city'] = str_replace('_', ' ', substr($key, strpos($key, '/') + 1));
    }
    return $timeZones;
  }

  /**
   * sorts the passed array according to the locale of this sfCultureInfo class
   *
   * @param  array the array to pe sorted wiht "asort" and this locale
   */
  public function sortArray(&$array)
  {
    $oldLocale = setlocale(LC_COLLATE, 0);
    setlocale(LC_COLLATE, $this->getName());
    asort($array, SORT_LOCALE_STRING);
    setlocale(LC_COLLATE, $oldLocale);
  }
}
