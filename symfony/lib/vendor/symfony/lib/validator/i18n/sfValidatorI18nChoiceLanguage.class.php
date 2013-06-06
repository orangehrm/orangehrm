<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfValidatorI18nChoiceLanguage validates than the value is a valid language.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfValidatorI18nChoiceLanguage.class.php 23940 2009-11-14 17:58:19Z fabien $
 */
class sfValidatorI18nChoiceLanguage extends sfValidatorChoice
{
  /**
   * Configures the current validator.
   *
   * Available options:
   *
   *  * languages: An array of language codes to use
   *
   * @param array $options   An array of options
   * @param array $messages  An array of error messages
   *
   * @see sfValidatorChoice
   */
  protected function configure($options = array(), $messages = array())
  {
    parent::configure($options, $messages);

    $this->addOption('languages');

    // populate choices with all languages
    $languages = array_keys(sfCultureInfo::getInstance()->getLanguages());

    // restrict languages to a sub-set
    if (isset($options['languages']))
    {
      if ($problems = array_diff($options['languages'], $languages))
      {
        throw new InvalidArgumentException(sprintf('The following languages do not exist: %s.', implode(', ', $problems)));
      }

      $languages = $options['languages'];
    }

    $this->setOption('choices', $languages);
  }
}
