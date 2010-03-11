<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfValidatorSchemaFilter executes non schema validator on a schema input value.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfValidatorSchemaFilter.class.php 11003 2008-08-20 16:39:20Z fabien $
 */
class sfValidatorSchemaFilter extends sfValidatorSchema
{
  /**
   * Constructor.
   *
   * @param string          $field      The field name
   * @param sfValidatorBase $validator  The validator
   * @param array           $options    An array of options
   * @param array           $messages   An array of error messages
   *
   * @see sfValidatorBase
   */
  public function __construct($field, sfValidatorBase $validator, $options = array(), $messages = array())
  {
    $this->addOption('field', $field);
    $this->addOption('validator', $validator);

    parent::__construct(null, $options, $messages);
  }

  /**
   * @see sfValidatorBase
   */
  protected function doClean($values)
  {
    if (is_null($values))
    {
      $values = array();
    }

    if (!is_array($values))
    {
      throw new InvalidArgumentException('You must pass an array parameter to the clean() method');
    }

    $value = isset($values[$this->getOption('field')]) ? $values[$this->getOption('field')] : null;

    try
    {
      $values[$this->getOption('field')] = $this->getOption('validator')->clean($value);
    }
    catch (sfValidatorError $error)
    {
      throw new sfValidatorErrorSchema($this, array($this->getOption('field') => $error));
    }

    return $values;
  }

  /**
   * @see sfValidatorBase
   */
  public function asString($indent = 0)
  {
    return sprintf('%s%s:%s', str_repeat(' ', $indent), $this->getOption('field'), $this->getOption('validator')->asString(0));
  }
}
