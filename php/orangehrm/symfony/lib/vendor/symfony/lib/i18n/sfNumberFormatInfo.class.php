<?php

/**
 * sfNumberFormatInfo class file.
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
 * @version    $Id: sfNumberFormatInfo.class.php 11700 2008-09-21 10:53:44Z fabien $
 * @package    symfony
 * @subpackage i18n
 */
 
/**
 * sfNumberFormatInfo class
 * 
 * Defines how numeric values are formatted and displayed,
 * depending on the culture. Numeric values are formatted using
 * standard or custom patterns stored in the properties of a 
 * sfNumberFormatInfo. 
 *
 * This class contains information, such as currency, decimal 
 * separators, and other numeric symbols.
 *
 * To create a sfNumberFormatInfo for a specific culture, 
 * create a sfCultureInfo for that culture and retrieve the
 * sfCultureInfo->NumberFormat property. Or use 
 * sfNumberFormatInfo::getInstance($culture).
 * To create a sfNumberFormatInfo for the invariant culture, use the 
 * InvariantInfo::getInvariantInfo(). 
 *
 *
 * @author Xiang Wei Zhuo <weizhuo[at]gmail[dot]com>
 * @version v1.0, last update on Sun Dec 05 14:48:26 EST 2004
 * @package    symfony
 * @subpackage i18n
 */
class sfNumberFormatInfo
{
  /**
   * ICU number formatting data.
   * @var array
   */
  protected $data = array();

  /**
   * A list of properties that are accessable/writable.
   * @var array
   */
  protected $properties = array();

  /**
   * The number pattern.
   * @var array
   */
  protected $pattern = array();

  const DECIMAL = 0;
  const CURRENCY = 1;
  const PERCENTAGE = 2;
  const SCIENTIFIC = 3;

  /**
   * Allows functions that begins with 'set' to be called directly
   * as an attribute/property to retrieve the value.
   *
   * @return mixed
   */
  function __get($name)
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
   */
  function __set($name, $value)
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
   * Initializes a new writable instance of the sfNumberFormatInfo class 
   * that is dependent on the ICU data for number, decimal, and currency
   * formatting information. <b>N.B.</b>You should not initialize this 
   * class directly unless you know what you are doing. Please use use 
   * sfNumberFormatInfo::getInstance() to create an instance.
   *
   * @param array $data ICU data for date time formatting.
   * @param int   $type The sfNumberFormatInfo type
   * @see getInstance()
   */
  function __construct($data = array(), $type = sfNumberFormatInfo::DECIMAL)
  {
    $this->properties = get_class_methods($this);

    if (empty($data))
    {
      throw new sfException('Please provide the ICU data to initialize.');
    }

    $this->data = $data;

    $this->setPattern($type);
  }

  /**
   * Sets the pattern for a specific number pattern. The validate patterns
   * sfNumberFormatInfo::DECIMAL, sfNumberFormatInfo::CURRENCY,
   * sfNumberFormatInfo::PERCENTAGE, or sfNumberFormatInfo::SCIENTIFIC
   *
   * @param int $type pattern type.
   */
  function setPattern($type = sfNumberFormatInfo::DECIMAL)
  {
    if (is_int($type))
    {
      $this->pattern = $this->parsePattern($this->data['NumberPatterns'][$type]);
    }
    else
    {
      $this->pattern = $this->parsePattern($type);
    }

    $this->pattern['negInfty'] = $this->data['NumberElements'][6].$this->data['NumberElements'][9];

    $this->pattern['posInfty'] = $this->data['NumberElements'][11].$this->data['NumberElements'][9];
  }

  function getPattern()
  {
    return $this->pattern;
  }

  /**
   * Gets the default sfNumberFormatInfo that is culture-independent (invariant).
   *
   * @return sfNumberFormatInfo default sfNumberFormatInfo. 
   */
  static public function getInvariantInfo($type = sfNumberFormatInfo::DECIMAL)
  {
    static $invariant;
    if (is_null($invariant))
    {
      $culture = sfCultureInfo::getInvariantCulture();
      $invariant = $culture->NumberFormat;
      $invariant->setPattern($type);
    }

    return $invariant;
  }

  /**
   * Returns the sfNumberFormatInfo associated with the specified culture.
   *
   * @param sfCultureInfo $culture  the culture that gets the sfNumberFormat property.
   * @param int           $type     the number formatting type, it should be 
   * sfNumberFormatInfo::DECIMAL, sfNumberFormatInfo::CURRENCY,
   * sfNumberFormatInfo::PERCENTAGE, or sfNumberFormatInfo::SCIENTIFIC
   * @return sfNumberFormatInfo sfNumberFormatInfo for the specified culture.
   * @see getCurrencyInstance();
   * @see getPercentageInstance();
   * @see getScientificInstance();
   */
  public static function getInstance($culture = null, $type = sfNumberFormatInfo::DECIMAL)
  {
    if ($culture instanceof sfCultureInfo)
    {
      $formatInfo = $culture->getNumberFormat();
      $formatInfo->setPattern($type);

      return $formatInfo;
    }
    else if (is_string($culture))
    {
      $sfCultureInfo = sfCultureInfo::getInstance($culture);
      $formatInfo = $sfCultureInfo->getNumberFormat();
      $formatInfo->setPattern($type);

      return $formatInfo;
    }
    else
    {
      $sfCultureInfo = sfCultureInfo::getInstance();
      $formatInfo = $sfCultureInfo->getNumberFormat();
      $formatInfo->setPattern($type);

      return $formatInfo;
    }
  }

  /**
   * Returns the currency format info associated with the specified culture.
   *
   * @param sfCultureInfo $culture the culture that gets the NumberFormat property.
   * @return sfNumberFormatInfo sfNumberFormatInfo for the specified culture.
   */
  public static function getCurrencyInstance($culture = null)
  {
    return self::getInstance($culture, self::CURRENCY);
  }

  /**
   * Returns the percentage format info associated with the specified culture.
   *
   * @param sfCultureInfo $culture the culture that gets the NumberFormat property.
   * @return sfNumberFormatInfo sfNumberFormatInfo for the specified culture. 
   */
  public static function getPercentageInstance($culture = null)
  {
    return self::getInstance($culture, self::PERCENTAGE);
  }

  /**
   * Returns the scientific format info associated with the specified culture.
   *
   * @param sfCultureInfo $culture the culture that gets the NumberFormat property.
   * @return sfNumberFormatInfo sfNumberFormatInfo for the specified culture. 
   */
  public static function getScientificInstance($culture = null)
  {
    return self::getInstance($culture, self::SCIENTIFIC);
  }

  /**
   * Parses the given pattern and return a list of known properties.
   *
   * @param string $pattern a number pattern.
   * @return array list of pattern properties.
   */
  protected function parsePattern($pattern)
  {
    $pattern = explode(';', $pattern);

    $negative = null;
    if (count($pattern) > 1)
    {
      $negative = $pattern[1];
    }
    $pattern = $pattern[0];

    $comma = ',';
    $dot = '.';
    $digit = '0';
    $hash = '#';

    // find the first group point, and decimal point
    $groupPos1 = strrpos($pattern, $comma);
    $decimalPos = strrpos($pattern, $dot);

    $groupPos2 = false;
    $groupSize1 = false;
    $groupSize2 = false;
    $decimalPoints = is_int($decimalPos) ? -1 : false;

    $info['negPref'] = $this->data['NumberElements'][6];
    $info['negPost'] = '';

    $info['negative'] = $negative;
    $info['positive'] = $pattern;

    // find the negative prefix and postfix
    if ($negative)
    {
      $prefixPostfix = $this->getPrePostfix($negative);
      $info['negPref'] = $prefixPostfix[0];
      $info['negPost'] = $prefixPostfix[1];
    }

    $posfix = $this->getPrePostfix($pattern);
    $info['posPref'] = $posfix[0];
    $info['posPost'] = $posfix[1];

    if (is_int($groupPos1))
    {
      // get the second group
      $groupPos2 = strrpos(substr($pattern, 0, $groupPos1), $comma);

      // get the number of decimal digits
      if (is_int($decimalPos))
      {
        $groupSize1 = $decimalPos - $groupPos1 - 1;
      }
      else
      {
        // no decimal point, so traverse from the back
        // to find the groupsize 1.
        for ($i = strlen($pattern) - 1; $i >= 0; $i--)
        {
          if ($pattern{$i} == $digit || $pattern{$i} == $hash)
          {
            $groupSize1 = $i - $groupPos1;
            break;
          }
        }
      }

      // get the second group size
      if (is_int($groupPos2))
      {
        $groupSize2 = $groupPos1 - $groupPos2 - 1;
      }
    }

    if (is_int($decimalPos))
    {
      for ($i = strlen($pattern) - 1; $i >= 0; $i--)
      {
        if ($pattern{$i} == $dot)
        {
          break;
        }
        if ($pattern{$i} == $digit)
        {
          $decimalPoints = $i - $decimalPos;
          break;
        }
      }
    }

    $digitPattern = is_int($decimalPos) ? substr($pattern, 0, $decimalPos) : $pattern;
    $digitPattern  = preg_replace('/[^0]/', '', $digitPattern);

    $info['groupPos1']     = $groupPos1;
    $info['groupSize1']    = $groupSize1;
    $info['groupPos2']     = $groupPos2;
    $info['groupSize2']    = $groupSize2;
    $info['decimalPos']    = $decimalPos;
    $info['decimalPoints'] = $decimalPoints;
    $info['digitSize']     = strlen($digitPattern);

    return $info;
  }

  /**
   * Gets the prefix and postfix of a pattern.
   *
   * @param string $pattern pattern
   * @return array of prefix and postfix, array(prefix,postfix). 
   */
  protected function getPrePostfix($pattern)
  {
    $regexp = '/[#,\.0]+/';
    $result = preg_split($regexp, $pattern);

    return array($result[0], $result[1]);
  }

  /**
   * Indicates the number of decimal places.
   *
   * @return int number of decimal places.
   */
  function getDecimalDigits()
  {
    return $this->pattern['decimalPoints'];
  }

  /**
   * Sets the number of decimal places.
   *
   * @param int $value number of decimal places.
   */
  function setDecimalDigits($value)
  {
    return $this->pattern['decimalPoints'] = $value;
  }

  /**
   * Indicates the digit size.
   *
   * @return int digit size.
   */
  function getDigitSize()
  {
    return $this->pattern['digitSize'];
  }

  /**
   * Sets the digit size.
   *
   * @param int $value digit size.
   */
  function setDigitSize($value)
  {
    $this->pattern['digitSize'] = $value;
  }

  /**
   * Gets the string to use as the decimal separator.
   *
   * @return string decimal separator.
   */
  function getDecimalSeparator()
  {
    return $this->data['NumberElements'][0];
  }

  /**
   * Sets the string to use as the decimal separator.
   *
   * @param string $value the decimal point
   */
  function setDecimalSeparator($value)
  {
    return $this->data['NumberElements'][0] = $value;
  }

  /**
   * Gets the string that separates groups of digits to the left 
   * of the decimal in currency values.
   *
   * @return string currency group separator. 
   */
  function getGroupSeparator()
  {
    return $this->data['NumberElements'][1];
  }

  /**
   * Sets the string to use as the group separator.
   *
   * @param string $value the group separator.
   */
  function setGroupSeparator($value)
  {
    return $this->data['NumberElements'][1] = $value;
  }

  /**
   * Gets the number of digits in each group to the left of the decimal
   * There can be two grouping sizes, this fucntion
   * returns <b>array(group1, group2)</b>, if there is only 1 grouping size,
   * group2 will be false.
   *
   * @return array grouping size(s). 
   */
  function getGroupSizes()
  {
    $group1 = $this->pattern['groupSize1'];
    $group2 = $this->pattern['groupSize2'];

    return array($group1, $group2);
  }

  /**
   * Sets the number of digits in each group to the left of the decimal.
   * There can be two grouping sizes, the value should
   * be an <b>array(group1, group2)</b>, if there is only 1 grouping size,
   * group2 should be false.
   *
   * @param array $groupSize grouping size(s).
   */
  function setGroupSizes($groupSize)
  {
    $this->pattern['groupSize1'] = $groupSize[0];
    $this->pattern['groupSize2'] = $groupSize[1];
  }

  /**
   * Gets the format pattern for negative values.
   * The negative pattern is composed of a prefix, and postfix.
   * This function returns <b>array(prefix, postfix)</b>.
   *
   * @return arary negative pattern. 
   */
  function getNegativePattern()
  {
    $prefix = $this->pattern['negPref'];
    $postfix = $this->pattern['negPost'];

    return array($prefix, $postfix);
  }

  /**
   * Sets the format pattern for negative values.
   * The negative pattern is composed of a prefix, and postfix in the form
   * <b>array(prefix, postfix)</b>.
   *
   * @param arary $pattern negative pattern. 
   */
  function setNegativePattern($pattern)
  {
    $this->pattern['negPref'] = $pattern[0];
    $this->pattern['negPost'] = $pattern[1];
  }

  /**
   * Gets the format pattern for positive values.
   * The positive pattern is composed of a prefix, and postfix.
   * This function returns <b>array(prefix, postfix)</b>.
   *
   * @return arary positive pattern. 
   */
  function getPositivePattern()
  {
    $prefix = $this->pattern['posPref'];
    $postfix = $this->pattern['posPost'];

    return array($prefix, $postfix);
  }

  /**
   * Sets the format pattern for positive values.
   * The positive pattern is composed of a prefix, and postfix in the form
   * <b>array(prefix, postfix)</b>.
   *
   * @param arary $pattern positive pattern. 
   */
  function setPositivePattern($pattern)
  {
    $this->pattern['posPref'] = $pattern[0];
    $this->pattern['posPost'] = $pattern[1];
  }

  /**
   * Gets the string to use as the currency symbol.
   *
   * @return string $currency currency symbol. 
   */
  function getCurrencySymbol($currency = 'USD')
  {
    if (isset($this->pattern['symbol']))
    {
      return $this->pattern['symbol'];
    }
    else
    {
      return $this->data['Currencies'][$currency][0];
    }
  }

  /**
   * Sets the string to use as the currency symbol.
   *
   * @param string $symbol currency symbol.
   */
  function setCurrencySymbol($symbol)
  {
    $this->pattern['symbol'] = $symbol;
  }

  /**
   * Gets the string that represents negative infinity.
   *
   * @return string negative infinity.
   */
  function getNegativeInfinitySymbol()
  {
    return $this->pattern['negInfty'];
  }

  /**
   * Sets the string that represents negative infinity.
   *
   * @param string $value negative infinity. 
   */
  function setNegativeInfinitySymbol($value)
  {
    $this->pattern['negInfty'] = $value;
  }

  /**
   * Gets the string that represents positive infinity.
   *
   * @return string positive infinity. 
   */
  function getPositiveInfinitySymbol()
  {
    return $this->pattern['posInfty'];
  }

  /**
   * Sets the string that represents positive infinity.
   *
   * @param string $value positive infinity. 
   */
  function setPositiveInfinitySymbol($value)
  {
    $this->pattern['posInfty'] = $value;
  }

  /**
   * Gets the string that denotes that the associated number is negative.
   *
   * @return string negative sign. 
   */
  function getNegativeSign()
  {
    return $this->data['NumberElements'][6];
  }

  /**
   * Sets the string that denotes that the associated number is negative.
   *
   * @param string $value negative sign. 
   */
  function setNegativeSign($value)
  {
    $this->data['NumberElements'][6] = $value;
  }

  /**
   * Gets the string that denotes that the associated number is positive.
   *
   * @return string positive sign. 
   */
  function getPositiveSign()
  {
    return $this->data['NumberElements'][11];
  }

  /**
   * Sets the string that denotes that the associated number is positive.
   *
   * @param string $value positive sign. 
   */
  function setPositiveSign($value)
  {
    $this->data['NumberElements'][11] = $value;
  }

  /**
   * Gets the string that represents the IEEE NaN (not a number) value.
   *
   * @return string NaN symbol.
   */
  function getNaNSymbol()
  {
    return $this->data['NumberElements'][10];
  }

  /**
   * Sets the string that represents the IEEE NaN (not a number) value.
   *
   * @param string $value NaN symbol.
   */
  function setNaNSymbol($value)
  {
    $this->data['NumberElements'][10] = $value;
  }

  /**
   * Gets the string to use as the percent symbol.
   *
   * @return string percent symbol.
   */
  function getPercentSymbol()
  {
    return $this->data['NumberElements'][3];
  }

  /**
   * Sets the string to use as the percent symbol.
   *
   * @param string $value percent symbol.
   */
  function setPercentSymbol($value)
  {
    $this->data['NumberElements'][3] = $value;
  }

  /**
   * Gets the string to use as the per mille symbol.
   *
   * @return string percent symbol.
   */
  function getPerMilleSymbol()
  {
    return $this->data['NumberElements'][8];
  }

  /**
   * Sets the string to use as the per mille symbol.
   *
   * @param string $value percent symbol.
   */
  function setPerMilleSymbol($value)
  {
    $this->data['NumberElements'][8] = $value;
  }
}
