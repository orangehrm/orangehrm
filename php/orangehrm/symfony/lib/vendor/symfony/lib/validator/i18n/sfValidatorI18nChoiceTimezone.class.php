<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfValidatorI18nChoiceLanguage validates than the value is a valid timezone.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfValidatorI18nChoiceTimezone.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class sfValidatorI18nChoiceTimezone extends sfValidatorChoice
{
  /**
   * Configures the current validator.
   *
   * Available options:
   *
   * @param array $options   An array of options
   * @param array $messages  An array of error messages
   *
   * @see sfValidatorChoice
   */
  protected function configure($options = array(), $messages = array())
  {
    parent::configure($options, $messages);

    $this->setOption('choices', array_keys(sfCultureInfo::getInstance()->getTimeZones()));
  }
}
