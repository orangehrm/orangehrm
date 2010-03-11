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
 * sfDoctrineUniqueValidator validates that the value does not already exists
 *
 * <b>Required parameters:</b>
 *
 * # <b>class</b>         - [none]               - Doctrine class name.
 * # <b>column</b>        - [none]               - Doctrine column name.
 *
 * <b>Optional parameters:</b>
 *
 * # <b>unique_error</b>  - [Uniqueness error]   - An error message to use when
 *                                                the value for this column already
 *                                                exists in the database.
 *
 * @package    symfony
 * @subpackage doctrine
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfDoctrineUniqueValidator.class.php 11629 2008-09-17 22:22:25Z Jonathan.Wage $
 */
class sfDoctrineUniqueValidator extends sfValidator
{
  /**
   * execute
   *
   * @param string $value
   * @param string $error
   * @return void
   */
  public function execute(&$value, &$error)
  {
    $className  = $this->getParameter('class');
    $columnName = $className.'.'.$this->getParameter('column');

    $primaryKeys = Doctrine::getTable($className)->getIdentifier();
    if (!is_array($primaryKeys))
    {
      $primaryKeys = array($primaryKeys);
    }

    // implied assumption: the is at least one primary key
    foreach ($primaryKeys as $primaryKey)
    {
      if (is_null($primaryKeyValue = $this->getContext()->getRequest()->getParameter($primaryKey)))
      {
        break;
      }
    }

    $query = Doctrine_Query::create()
              ->from($className);

    if ($primaryKeyValue == null)
    {
      $query->where($columnName.' = ?');
      $res = $query->execute(array($value));
    } else {
      $query->where($columnName.' = ? AND '.$primaryKey.' != ?');
      $res = $query->execute(array($value, $primaryKeyValue));
    }

    if ($res->count())
    {
      $error = $this->getParameterHolder()->get('unique_error');

      return false;
    }

    return true;
  }

  /**
   * Initialize this validator.
   *
   * @param sfContext The current application context.
   * @param array   An associative array of initialization parameters.
   *
   * @return bool true, if initialization completes successfully, otherwise false.
   */
  public function initialize($context, $parameters = null)
  {
    // initialize parent
    parent::initialize($context);

    // set defaults
    $this->setParameter('unique_error', 'Uniqueness error');

    $this->getParameterHolder()->add($parameters);

    // check parameters
    if (!$this->getParameter('class'))
    {
      throw new sfValidatorException('The "class" parameter is mandatory for the sfDoctrineUniqueValidator validator.');
    }

    if (!$this->getParameter('column'))
    {
      throw new sfValidatorException('The "column" parameter is mandatory for the sfDoctrineUniqueValidator validator.');
    }

    return true;
  }
}