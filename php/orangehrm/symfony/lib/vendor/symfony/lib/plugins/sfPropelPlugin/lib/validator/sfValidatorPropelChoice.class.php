<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfValidatorPropelChoice validates that the value is one of the rows of a table.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfValidatorPropelChoice.class.php 28632 2010-03-20 14:13:37Z Kris.Wallsmith $
 */
class sfValidatorPropelChoice extends sfValidatorBase
{
  /**
   * Configures the current validator.
   *
   * Available options:
   *
   *  * model:      The model class (required)
   *  * criteria:   A criteria to use when retrieving objects
   *  * column:     The column name (null by default which means we use the primary key)
   *                must be in field name format
   *  * connection: The Propel connection to use (null by default)
   *  * multiple:   true if the select tag must allow multiple selections
   *  * min:        The minimum number of values that need to be selected (this option is only active if multiple is true)
   *  * max:        The maximum number of values that need to be selected (this option is only active if multiple is true)
   *
   * @see sfValidatorBase
   */
  protected function configure($options = array(), $messages = array())
  {
    $this->addRequiredOption('model');
    $this->addOption('criteria', null);
    $this->addOption('column', null);
    $this->addOption('connection', null);
    $this->addOption('multiple', false);
    $this->addOption('min');
    $this->addOption('max');

    $this->addMessage('min', 'At least %min% values must be selected (%count% values selected).');
    $this->addMessage('max', 'At most %max% values must be selected (%count% values selected).');
  }

  /**
   * @see sfValidatorBase
   */
  protected function doClean($value)
  {
    $criteria = null === $this->getOption('criteria') ? new Criteria() : clone $this->getOption('criteria');

    if ($this->getOption('multiple'))
    {
      if (!is_array($value))
      {
        $value = array($value);
      }

      $count = count($value);

      if ($this->hasOption('min') && $count < $this->getOption('min'))
      {
        throw new sfValidatorError($this, 'min', array('count' => $count, 'min' => $this->getOption('min')));
      }

      if ($this->hasOption('max') && $count > $this->getOption('max'))
      {
        throw new sfValidatorError($this, 'max', array('count' => $count, 'max' => $this->getOption('max')));
      }

      $criteria->addAnd($this->getColumn(), $value, Criteria::IN);

      $dbcount = call_user_func(array(constant($this->getOption('model').'::PEER'), 'doCount'), $criteria, false, $this->getOption('connection'));

      if ($dbcount != $count)
      {
        throw new sfValidatorError($this, 'invalid', array('value' => $value));
      }
    }
    else
    {
      $criteria->addAnd($this->getColumn(), $value);

      $dbcount = call_user_func(array(constant($this->getOption('model').'::PEER'), 'doCount'), $criteria, false, $this->getOption('connection'));

      if (0 === $dbcount)
      {
        throw new sfValidatorError($this, 'invalid', array('value' => $value));
      }
    }

    return $value;
  }

  /**
   * Returns the column to use for comparison.
   *
   * The primary key is used by default.
   *
   * @return string The column name
   */
  protected function getColumn()
  {
    if ($this->getOption('column'))
    {
      $columnName = $this->getOption('column');
      $from = BasePeer::TYPE_FIELDNAME;
    }
    else
    {
      $map = call_user_func(array(constant($this->getOption('model').'::PEER'), 'getTableMap'));
      foreach ($map->getColumns() as $column)
      {
        if ($column->isPrimaryKey())
        {
          $columnName = $column->getPhpName();
          break;
        }
      }
      $from = BasePeer::TYPE_PHPNAME;
    }

    return call_user_func(array(constant($this->getOption('model').'::PEER'), 'translateFieldName'), $columnName, $from, BasePeer::TYPE_COLNAME);
  }
}
