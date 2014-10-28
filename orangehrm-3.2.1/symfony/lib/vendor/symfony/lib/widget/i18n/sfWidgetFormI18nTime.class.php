<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWidgetFormI18nTime represents a time widget.
 *
 * @package    symfony
 * @subpackage widget
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfWidgetFormI18nTime.class.php 9046 2008-05-19 08:13:51Z FabianLange $
 */
class sfWidgetFormI18nTime extends sfWidgetFormTime
{
  /**
   * Constructor.
   *
   * Available options:
   *
   *  * culture: The culture to use for internationalized strings (required)
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetFormTime
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);

    $this->addRequiredOption('culture');

    $culture = isset($options['culture']) ? $options['culture'] : 'en';

    // format
    $this->setOption('format', $this->getTimeFormat($culture, true));

    // format_without_seconds
    $this->setOption('format_without_seconds', $this->getTimeFormat($culture, false));
  }

  protected function getTimeFormat($culture, $withSeconds)
  {
    $timeFormat = $withSeconds ? sfDateTimeFormatInfo::getInstance($culture)->getMediumTimePattern() : sfDateTimeFormatInfo::getInstance($culture)->getShortTimePattern();

    if (false === ($hourPos = stripos($timeFormat, 'h')) || false === ($minutePos = stripos($timeFormat, 'm')))
    {
      return $this->getOption('format');
    }

    $trans = array(
      substr($timeFormat, $hourPos,   strripos($timeFormat, 'h') - $hourPos + 1)   => '%hour%',
      substr($timeFormat, $minutePos, strripos($timeFormat, 'm') - $minutePos + 1) => '%minute%',
    );

    if ($withSeconds)
    {
      if (false === $secondPos = stripos($timeFormat, 's'))
      {
        return $this->getOption('format');
      }

      $trans[substr($timeFormat, $secondPos, strripos($timeFormat, 's') - $secondPos + 1)] = '%second%';
    }

    return strtr($timeFormat, $trans);
  }
}
