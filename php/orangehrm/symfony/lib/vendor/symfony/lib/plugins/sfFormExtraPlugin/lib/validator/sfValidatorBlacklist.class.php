<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfValidatorBlacklist validates than the value is not one of the configured 
 * forbidden values. This is a kind of opposite of the sfValidatorChoice 
 * validator.
 * 
 * @package    symfony
 * @subpackage validator
 * @author     Nicolas Perriault <nicolas.perriault@symfony-project.com>
 * @version    SVN: $Id: sfValidatorChoice.class.php 9048 2008-05-19 09:11:23Z FabianLange $
 */
class sfValidatorBlacklist extends sfValidatorBase
{
  /**
   * Configures the current validator.
   *
   * Available options:
   *
   *  * forbidden_values: An array of forbidden values (required)
   *  * case_sensitive:   Case sensitive comparison (default true)
   *
   * @param array $options    An array of options
   * @param array $messages   An array of error messages
   *
   * @see sfValidatorBase
   */
  protected function configure($options = array(), $messages = array())
  {
    $this->addRequiredOption('forbidden_values');
    $this->addOption('case_sensitive', true);
  }

  /**
   * @see sfValidatorBase
   */
  protected function doClean($value)
  {
    $forbiddenValues = $this->getOption('forbidden_values');
    if ($forbiddenValues instanceof sfCallable)
    {
      $forbiddenValues = $forbiddenValues->call();
    }
    
    $checkValue = $value;
    
    if (false === $this->getOption('case_sensitive'))
    {
      $checkValue = strtolower($checkValue);
      $forbiddenValues = array_map('strtolower', $forbiddenValues);
    }
    
    if (in_array($checkValue, $forbiddenValues))
    {
      throw new sfValidatorError($this, 'invalid', array('value' => $value));
    }

    return $checkValue;
  }
}
