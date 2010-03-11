<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) Jonathan H. Wage <jonwage@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfValidatorDoctrineChoice validates that the value is one of the rows of a table.
 *
 * @package    symfony
 * @subpackage doctrine
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfValidatorDoctrineChoice.class.php 8804 2008-05-06 12:11:10Z fabien $
 */
class sfValidatorDoctrineChoice extends sfValidatorBase
{
  /**
   * Configures the current validator.
   *
   * Available options:
   *
   *  * model:      The model class (required)
   *  * alias:      The alias of the root component used in the query
   *  * query:      A query to use when retrieving objects
   *  * column:     The column name (null by default which means we use the primary key)
   *                must be in field name format
   *  * connection: The Doctrine connection to use (null by default)
   *
   * @see sfValidatorBase
   */
  protected function configure($options = array(), $messages = array())
  {
    $this->addRequiredOption('model');
    $this->addOption('alias', 'a');
    $this->addOption('query', null);
    $this->addOption('column', null);
    $this->addOption('connection', null);
  }

  /**
   * @see sfValidatorBase
   */
  protected function doClean($value)
  {
    $a = ($q = $this->getOption('query')) ? $q->getRootAlias():$this->getOption('alias');
    $q = is_null($this->getOption('query')) ? Doctrine_Query::create()->from($this->getOption('model') . ' ' . $a) : $this->getOption('query');
    $q->addWhere($a . '.' . $this->getColumn() . ' = ?', $value);

    $object = $q->fetchOne();
    
    if (!$object)
    {
      throw new sfValidatorError($this, 'invalid', array('value' => $value));
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
    $table = Doctrine::getTable($this->getOption('model'));
    if ($this->getOption('column'))
    {
      $columnName = $this->getOption('column');
    }
    else
    {
      $identifier = (array) $table->getIdentifier();
      $columnName = current($identifier);
    }

    return $table->getColumnName($columnName);
  }
}
