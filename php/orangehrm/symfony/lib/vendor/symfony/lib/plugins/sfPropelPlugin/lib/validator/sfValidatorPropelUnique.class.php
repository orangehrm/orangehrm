<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfValidatorPropelUnique validates that the uniqueness of a column.
 *
 * Warning: sfValidatorPropelUnique is susceptible to race conditions.
 * To avoid this issue, wrap the validation process and the model saving
 * inside a transaction.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfValidatorPropelUnique.class.php 27940 2010-02-12 13:31:30Z Kris.Wallsmith $
 */
class sfValidatorPropelUnique extends sfValidatorSchema
{
  /**
   * Constructor.
   *
   * @param array  $options   An array of options
   * @param array  $messages  An array of error messages
   *
   * @see sfValidatorSchema
   */
  public function __construct($options = array(), $messages = array())
  {
    parent::__construct(null, $options, $messages);
  }

  /**
   * Configures the current validator.
   *
   * Available options:
   *
   *  * model:              The model class (required)
   *  * column:             The unique column name in Propel field name format (required)
   *                        If the uniqueness is for several columns, you can pass an array of field names
   *  * field               Field name used by the form, other than the column name
   *  * primary_key:        The primary key column name in Propel field name format (optional, will be introspected if not provided)
   *                        You can also pass an array if the table has several primary keys
   *  * connection:         The Propel connection to use (null by default)
   *  * throw_global_error: Whether to throw a global error (false by default) or an error tied to the first field related to the column option array
   *
   * @see sfValidatorBase
   */
  protected function configure($options = array(), $messages = array())
  {
    $this->addRequiredOption('model');
    $this->addRequiredOption('column');
    $this->addOption('field', null);
    $this->addOption('primary_key', null);
    $this->addOption('connection', null);
    $this->addOption('throw_global_error', false);

    $this->setMessage('invalid', 'An object with the same "%column%" already exist.');
  }

  /**
   * @see sfValidatorBase
   */
  protected function doClean($values)
  {
    if (!is_array($values))
    {
      throw new InvalidArgumentException('You must pass an array parameter to the clean() method (this validator can only be used as a post validator).');
    }

    if (!is_array($this->getOption('column')))
    {
      $this->setOption('column', array($this->getOption('column')));
    }
    $columns = $this->getOption('column');

    if (!is_array($field = $this->getOption('field')))
    {
      $this->setOption('field', $field ? array($field) : array());
    }
    $fields = $this->getOption('field');

    $criteria = new Criteria();
    foreach ($columns as $i => $column)
    {
      $name = isset($fields[$i]) ? $fields[$i] : $column;
      if (!array_key_exists($name, $values))
      {
        // one of the columns has be removed from the form
        return $values;
      }

      $colName = call_user_func(array(constant($this->getOption('model').'::PEER'), 'translateFieldName'), $column, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_COLNAME);

      $criteria->add($colName, $values[$name]);
    }

    $object = call_user_func(array(constant($this->getOption('model').'::PEER'), 'doSelectOne'), $criteria, $this->getOption('connection'));

    // if no object or if we're updating the object, it's ok
    if (null === $object || $this->isUpdate($object, $values))
    {
      return $values;
    }

    $error = new sfValidatorError($this, 'invalid', array('column' => implode(', ', $this->getOption('column'))));

    if ($this->getOption('throw_global_error'))
    {
      throw $error;
    }

    throw new sfValidatorErrorSchema($this, array(isset($fields[0]) ? $fields[0] : $columns[0] => $error));
  }

  /**
   * Returns whether the object is being updated.
   *
   * @param BaseObject  $object   A Propel object
   * @param array       $values   An array of values
   *
   * @return Boolean     true if the object is being updated, false otherwise
   */
  protected function isUpdate(BaseObject $object, $values)
  {
    // check each primary key column
    foreach ($this->getPrimaryKeys() as $column)
    {
      $columnPhpName = call_user_func(array(constant($this->getOption('model').'::PEER'), 'translateFieldName'), $column, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_PHPNAME);
      $method = 'get'.$columnPhpName;
      if (!isset($values[$column]) or $object->$method() != $values[$column])
      {
        return false;
      }
    }

    return true;
  }

  /**
   * Returns the primary keys for the model.
   *
   * @return array An array of primary keys
   */
  protected function getPrimaryKeys()
  {
    if (null === $this->getOption('primary_key'))
    {
      $primaryKeys = array();
      $tableMap = call_user_func(array(constant($this->getOption('model').'::PEER'), 'getTableMap'));
      foreach ($tableMap->getColumns() as $column)
      {
        if (!$column->isPrimaryKey())
        {
          continue;
        }

        $primaryKeys[] = call_user_func(array(constant($this->getOption('model').'::PEER'), 'translateFieldName'), $column->getPhpName(), BasePeer::TYPE_PHPNAME, BasePeer::TYPE_FIELDNAME);
      }

      $this->setOption('primary_key', $primaryKeys);
    }

    if (!is_array($this->getOption('primary_key')))
    {
      $this->setOption('primary_key', array($this->getOption('primary_key')));
    }

    return $this->getOption('primary_key');
  }
}
