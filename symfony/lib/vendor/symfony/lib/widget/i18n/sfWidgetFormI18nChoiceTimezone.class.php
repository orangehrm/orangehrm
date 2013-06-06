<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWidgetFormI18nChoiceTimezone represents a timezone choice widget.
 *
 * @package    symfony
 * @subpackage widget
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfWidgetFormI18nChoiceTimezone.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class sfWidgetFormI18nChoiceTimezone extends sfWidgetFormChoice
{
  /**
   * Constructor.
   *
   * Available options:
   *
   *  * culture:   The culture to use for internationalized strings
   *  * add_empty: Whether to add a first empty value or not (false by default)
   *               If the option is not a Boolean, the value will be used as the text value
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetFormChoice
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);

    $this->addOption('culture');
    $this->addOption('add_empty', false);

    $culture = isset($options['culture']) ? $options['culture'] : 'en';
    $timezones = array_keys(sfCultureInfo::getInstance($culture)->getTimeZones());
    $timezones = array_combine($timezones, $timezones);

    $addEmpty = isset($options['add_empty']) ? $options['add_empty'] : false;
    if (false !== $addEmpty)
    {
      $timezones = array_merge(array('' => true === $addEmpty ? '' : $addEmpty), $timezones);
    }

    $this->setOption('choices', $timezones);
  }
}
