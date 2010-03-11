<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfI18N wraps the core i18n classes for a symfony context.
 *
 * @package    symfony
 * @subpackage i18n
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfI18N.class.php 17749 2009-04-29 11:54:22Z fabien $
 */
class sfI18N
{
  protected
    $configuration = null,
    $dispatcher    = null,
    $cache         = null,
    $options       = array(),
    $culture       = 'en',
    $messageSource = null,
    $messageFormat = null;

  /**
   * Class constructor.
   *
   * @see initialize()
   */
  public function __construct(sfApplicationConfiguration $configuration, sfCache $cache = null, $options = array())
  {
    $this->initialize($configuration, $cache, $options);
  }

  /**
   * Initializes this class.
   *
   * Available options:
   *
   *  * culture:             The culture
   *  * source:              The i18n source (XLIFF by default)
   *  * debug:               Whether to enable debug or not (false by default)
   *  * database:            The database name (default by default)
   *  * untranslated_prefix: The prefix to use when a message is not translated
   *  * untranslated_suffix: The suffix to use when a message is not translated
   *
   * @param sfApplicationConfiguration $configuration   A sfApplicationConfiguration instance
   * @param sfCache                    $cache           A sfCache instance
   * @param array                      $options         An array of options
   */
  public function initialize(sfApplicationConfiguration $configuration, sfCache $cache = null, $options = array())
  {
    $this->configuration = $configuration;
    $this->dispatcher = $configuration->getEventDispatcher();
    $this->cache = $cache;

    if (isset($options['culture']))
    {
      $this->culture = $options['culture'];
      unset($options['culture']);
    }

    $this->options = array_merge(array(
      'source'              => 'XLIFF',
      'debug'               => false,
      'database'            => 'default',
      'untranslated_prefix' => '[T]',
      'untranslated_suffix' => '[/T]',
    ), $options);

    $this->dispatcher->connect('user.change_culture', array($this, 'listenToChangeCultureEvent'));

    if($this->isMessageSourceFileBased($this->options['source']))
    {
      $this->dispatcher->connect('controller.change_action', array($this, 'listenToChangeActionEvent'));
    }
  }

  /**
   * Returns the initialization options
   *
   * @return array The options used to initialize sfI18n
   */
  public function getOptions()
  {
    return $this->options;
  }

  /**
   * Returns the configuration instance.
   *
   * @return sfApplicationConfiguration An sfApplicationConfiguration instance
   */
  public function getConfiguration()
  {
    return $this->configuration;
  }

  /**
   * Sets the message source.
   *
   * @param mixed  $dirs    An array of i18n directories if message source is a sfMessageSource_File subclass, null otherwise
   * @param string $culture The culture
   */
  public function setMessageSource($dirs, $culture = null)
  {
    if (is_null($dirs))
    {
      $this->messageSource = $this->createMessageSource();
    }
    else
    {
      $this->messageSource = sfMessageSource::factory('Aggregate', array_map(array($this, 'createMessageSource'), $dirs));
    }

    if (!is_null($this->cache))
    {
      $this->messageSource->setCache($this->cache);
    }

    if (!is_null($culture))
    {
      $this->setCulture($culture);
    }
    else
    {
      $this->messageSource->setCulture($this->culture);
    }

    $this->messageFormat = null;
  }

  /**
   * Returns a new message source.
   *
   * @param  mixed $dir An array of i18n directories to create a XLIFF or gettext message source, null otherwise
   *
   * @return sfMessageSource A sfMessageSource object
   */
  public function createMessageSource($dir = null)
  {
    return sfMessageSource::factory($this->options['source'], self::isMessageSourceFileBased($this->options['source']) ? $dir : $this->options['database']);
  }

  /**
   * Gets the current culture for i18n format objects.
   *
   * @return string The culture
   */
  public function getCulture()
  {
    return $this->culture;
  }

  /**
   * Sets the current culture for i18n format objects.
   *
   * @param string $culture The culture
   */
  public function setCulture($culture)
  {
    $this->culture = $culture;

    // change user locale for formatting, collation, and internal error messages
    setlocale(LC_ALL, 'en_US.utf8', 'en_US.UTF8', 'en_US.utf-8', 'en_US.UTF-8');
    setlocale(LC_COLLATE, $culture.'.utf8', $culture.'.UTF8', $culture.'.utf-8', $culture.'.UTF-8');
    setlocale(LC_CTYPE, $culture.'.utf8', $culture.'.UTF8', $culture.'.utf-8', $culture.'.UTF-8');
    setlocale(LC_MONETARY, $culture.'.utf8', $culture.'.UTF8', $culture.'.utf-8', $culture.'.UTF-8');
    setlocale(LC_TIME, $culture.'.utf8', $culture.'.UTF8', $culture.'.utf-8', $culture.'.UTF-8');

    if ($this->messageSource)
    {
      $this->messageSource->setCulture($culture);
      $this->messageFormat = null;
    }
  }

  /**
   * Gets the message source.
   *
   * @return sfMessageSource A sfMessageSource object
   */
  public function getMessageSource()
  {
    if (!isset($this->messageSource))
    {
      $dirs = ($this->isMessageSourceFileBased($this->options['source'])) ? $this->configuration->getI18NGlobalDirs() : null;
      $this->setMessageSource($dirs, $this->culture);
    }

    return $this->messageSource;
  }

  /**
   * Gets the message format.
   *
   * @return sfMessageFormat A sfMessageFormat object
   */
  public function getMessageFormat()
  {
    if (!isset($this->messageFormat))
    {
      $this->messageFormat = new sfMessageFormat($this->getMessageSource(), sfConfig::get('sf_charset'));

      if ($this->options['debug'])
      {
        $this->messageFormat->setUntranslatedPS(array($this->options['untranslated_prefix'], $this->options['untranslated_suffix']));
      }
    }

    return $this->messageFormat;
  }

  /**
   * Gets the translation for the given string
   *
   * @param  string $string     The string to translate
   * @param  array  $args       An array of arguments for the translation
   * @param  string $catalogue  The catalogue name
   *
   * @return string The translated string
   */
  public function __($string, $args = array(), $catalogue = 'messages')
  {
    return $this->getMessageFormat()->format($string, $args, $catalogue);
  }

  /**
   * Gets a country name.
   *
   * @param  string $iso      The ISO code
   * @param  string $culture  The culture for the translation
   *
   * @return string The country name
   */
  public function getCountry($iso, $culture = null)
  {
    $c = sfCultureInfo::getInstance(is_null($culture) ? $this->culture : $culture);
    $countries = $c->getCountries();

    return (array_key_exists($iso, $countries)) ? $countries[$iso] : '';
  }

  /**
   * Gets a native culture name.
   *
   * @param  string $culture The culture
   *
   * @return string The culture name
   */
  public function getNativeName($culture)
  {
    return sfCultureInfo::getInstance($culture)->getNativeName();
  }

  /**
   * Returns a timestamp from a date with time formatted with a given culture.
   *
   * @param  string  $dateTime  The formatted date with time as string
   * @param  string  $culture The culture
   *
   * @return integer The timestamp
   */
  public function getTimestampForCulture($dateTime, $culture = null)
  {
    list($day, $month, $year) = $this->getDateForCulture($dateTime, is_null($culture) ? $this->culture : $culture);
    list($hour, $minute) = $this->getTimeForCulture($dateTime, is_null($culture) ? $this->culture : $culture);

    return is_null($day) ? null : mktime($hour, $minute, 0, $month, $day, $year);
  }

  /**
   * Returns the day, month and year from a date formatted with a given culture.
   *
   * @param  string  $date    The formatted date as string
   * @param  string  $culture The culture
   *
   * @return array   An array with the day, month and year
   */
  public function getDateForCulture($date, $culture = null)
  {
    if (!$date)
    {
      return null;
    }

    $dateFormatInfo = @sfDateTimeFormatInfo::getInstance(is_null($culture) ? $this->culture : $culture);
    $dateFormat = $dateFormatInfo->getShortDatePattern();

    // We construct the regexp based on date format
    $dateRegexp = preg_replace('/[dmy]+/i', '(\d+)', $dateFormat);

    // We parse date format to see where things are (m, d, y)
    $a = array(
      'd' => strpos($dateFormat, 'd'),
      'm' => strpos($dateFormat, 'M'),
      'y' => strpos($dateFormat, 'y'),
    );
    $tmp = array_flip($a);
    ksort($tmp);
    $i = 0;
    $c = array();
    foreach ($tmp as $value) $c[++$i] = $value;
    $datePositions = array_flip($c);

    // We find all elements
    if (preg_match("~$dateRegexp~", $date, $matches))
    {
      // We get matching timestamp
      return array($matches[$datePositions['d']], $matches[$datePositions['m']], $matches[$datePositions['y']]);
    }
    else
    {
      return null;
    }
  }

  /**
   * Returns the hour, minute from a date formatted with a given culture.
   *
   * @param  string  $time    The formatted date as string
   * @param  string  $culture The culture
   *
   * @return array   An array with the hour and minute
   */
  public function getTimeForCulture($time, $culture)
  {
    if (!$time) return 0;

    $culture = is_null($culture) ? $this->culture : $culture;

    $timeFormatInfo = @sfDateTimeFormatInfo::getInstance($culture);
    $timeFormat = $timeFormatInfo->getShortTimePattern();

    // We construct the regexp based on time format
    $timeRegexp = preg_replace(array('/[^hm:]+/i', '/[hm]+/i'), array('', '(\d+)'), $timeFormat);

    // We parse time format to see where things are (h, m)
    $a = array(
      'h' => strpos($timeFormat, 'H') !== false ? strpos($timeFormat, 'H') : strpos($timeFormat, 'h'),
      'm' => strpos($timeFormat, 'm')
    );
    $tmp = array_flip($a);
    ksort($tmp);
    $i = 0;
    $c = array();
    foreach ($tmp as $value) $c[++$i] = $value;
    $timePositions = array_flip($c);

    // We find all elements
    if (preg_match("~$timeRegexp~", $time, $matches))
    {
      // We get matching timestamp
      return array($matches[$timePositions['h']], $matches[$timePositions['m']]);
    }
    else
    {
      return null;
    }
  }

  /**
   * Returns true if messages are stored in a file.
   *
   * @param  string  $source  The source name
   *
   * @return Boolean true if messages are stored in a file, false otherwise
   */
  static public function isMessageSourceFileBased($source)
  {
    $class = 'sfMessageSource_'.$source;

    return class_exists($class) && is_subclass_of($class, 'sfMessageSource_File');
  }

  /**
   * Listens to the user.change_culture event.
   *
   * @param sfEvent $event  An sfEvent instance
   *
   */
  public function listenToChangeCultureEvent(sfEvent $event)
  {
    // change the message format object with the new culture
    $this->setCulture($event['culture']);
  }

  /**
   * Listens to the controller.change_action event.
   *
   * @param sfEvent $event An sfEvent instance
   *
   */
  public function listenToChangeActionEvent(sfEvent $event)
  {
    // change message source directory to our module
    $this->setMessageSource($this->configuration->getI18NDirs($event['module']));
  }

}
