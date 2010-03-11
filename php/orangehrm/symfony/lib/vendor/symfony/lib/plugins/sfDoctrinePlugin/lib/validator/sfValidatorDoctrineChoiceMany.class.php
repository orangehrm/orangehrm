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
 * sfValidatorDoctrineChoiceMany validates than an array of values is in the array of the existing rows of a table.
 *
 * @package    symfony
 * @subpackage doctrine
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfValidatorDoctrineChoiceMany.class.php 7902 2008-03-15 13:17:33Z fabien $
 */
class sfValidatorDoctrineChoiceMany extends sfValidatorDoctrineChoice
{
  /**
   * @see sfValidatorBase
   */
  protected function doClean($values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if(isset($values[0]) && !$values[0])
    {
      unset($values[0]);
    }

    $a = $this->getOption('alias');
    $q = is_null($this->getOption('query')) ? Doctrine_Query::create()->from($this->getOption('model') . ' ' . $a) : $this->getOption('query');
    $q = $q->andWhereIn($a . '.' . $this->getColumn(), $values);

    $objects = $q->execute();

    if (count($objects) != count($values))
    {
      throw new sfValidatorError($this, 'invalid', array('value' => $values));
    }

    return $values;
  }
}
