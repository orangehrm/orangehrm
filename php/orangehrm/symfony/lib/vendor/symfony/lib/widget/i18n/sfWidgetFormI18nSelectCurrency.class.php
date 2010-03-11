<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWidgetFormI18nSelectCurrency represents a currency HTML select tag.
 *
 * @package    symfony
 * @subpackage widget
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfWidgetFormI18nSelectCurrency.class.php 16232 2009-03-12 08:37:50Z fabien $
 */
class sfWidgetFormI18nSelectCurrency extends sfWidgetFormSelect
{
  /**
   * Constructor.
   *
   * Available options:
   *
   *  * culture:    The culture to use for internationalized strings (required)
   *  * currencies: An array of currency codes to use (ISO 4217)
   *  * add_empty:  Whether to add a first empty value or not (false by default)
   *                If the option is not a Boolean, the value will be used as the text value
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetFormSelect
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);

    $this->addRequiredOption('culture');
    $this->addOption('currencies');
    $this->addOption('add_empty', false);

    // populate choices with all currencies
    $culture = isset($options['culture']) ? $options['culture'] : 'en';

    $currencies = sfCultureInfo::getInstance($culture)->getCurrencies(isset($options['currencies']) ? $options['currencies'] : null);

    $addEmpty = isset($options['add_empty']) ? $options['add_empty'] : false;
    if (false !== $addEmpty)
    {
      $currencies = array_merge(array('' => true === $addEmpty ? '' : $addEmpty), $currencies);
    }

    $this->setOption('choices', $currencies);
  }
}
