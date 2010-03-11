<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWidgetFormI18nSelectLanguage represents a language HTML select tag.
 *
 * @package    symfony
 * @subpackage widget
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfWidgetFormI18nSelectLanguage.class.php 11842 2008-09-29 10:53:28Z fabien $
 */
class sfWidgetFormI18nSelectLanguage extends sfWidgetFormSelect
{
  /**
   * Constructor.
   *
   * Available options:
   *
   *  * culture:   The culture to use for internationalized strings (required)
   *  * languages: An array of language codes to use (ISO 639-1)
   *  * add_empty: Whether to add a first empty value or not (false by default)
   *               If the option is not a Boolean, the value will be used as the text value
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
    $this->addOption('languages');
    $this->addOption('add_empty', false);

    // populate choices with all languages
    $culture = isset($options['culture']) ? $options['culture'] : 'en';

    $languages = sfCultureInfo::getInstance($culture)->getLanguages(isset($options['languages']) ? $options['languages'] : null);

    $addEmpty = isset($options['add_empty']) ? $options['add_empty'] : false;
    if (false !== $addEmpty)
    {
      $languages = array_merge(array('' => true === $addEmpty ? '' : $addEmpty), $languages);
    }

    $this->setOption('choices', $languages);
  }
}
