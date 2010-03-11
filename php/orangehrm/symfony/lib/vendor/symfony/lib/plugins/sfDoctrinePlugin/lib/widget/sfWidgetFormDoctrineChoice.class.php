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
 * sfWidgetFormDoctrineChoice represents a choice widget for a model.
 *
 * @package    symfony
 * @subpackage doctrine
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfWidgetFormDoctrineChoice.class.php 11540 2008-09-14 15:23:55Z fabien $
 */
class sfWidgetFormDoctrineChoice extends sfWidgetFormChoice
{
  /**
   * @see sfWidget
   */
  public function __construct($options = array(), $attributes = array())
  {
    $options['choices'] = new sfCallable(array($this, 'getChoices'));

    parent::__construct($options, $attributes);
  }

  /**
   * Constructor.
   *
   * Available options:
   *
   *  * model:        The model class (required)
   *  * add_empty:    Whether to add a first empty value or not (false by default)
   *                  If the option is not a Boolean, the value will be used as the text value
   *  * method:       The method to use to display object values (__toString by default)
   *  * key_method:   The method to use to display the object keys (getPrimaryKey by default)
   *  * order_by:     An array composed of two fields:
   *                    * The column to order by the results (must be in the PhpName format)
   *                    * asc or desc
   *  * query:        A query to use when retrieving objects
   *  * connection:   The Doctrine connection to use (null by default)
   *  * multiple:     true if the select tag must allow multiple selections
   *  * table_method: The method to use to display the object keys (getPrimaryKey by default)
   *
   * @see sfWidgetFormSelect
   */
  protected function configure($options = array(), $attributes = array())
  {
    $this->addRequiredOption('model');
    $this->addOption('add_empty', false);
    $this->addOption('method', '__toString');
    $this->addOption('key_method', 'getPrimaryKey');
    $this->addOption('order_by', null);
    $this->addOption('query', null);
    $this->addOption('connection', null);
    $this->addOption('multiple', false);
    $this->addOption('table_method', null);

    parent::configure($options, $attributes);
  }

  /**
   * Returns the choices associated to the model.
   *
   * @return array An array of choices
   */
  public function getChoices()
  {
    $choices = array();
    if (false !== $this->getOption('add_empty'))
    {
      $choices[''] = true === $this->getOption('add_empty') ? '' : $this->getOption('add_empty');
    }

    if (is_null($this->getOption('table_method')))
    {
      $query = is_null($this->getOption('query')) ? Doctrine::getTable($this->getOption('model'))->createQuery() : $this->getOption('query');
      if ($order = $this->getOption('order_by'))
      {
        $query->addOrderBy($order[0] . ' ' . $order[1]);
      }
      $objects = $query->execute();
    } else {
      $tableMethod = $this->getOption('table_method');
      $results = Doctrine::getTable($this->getOption('model'))->$tableMethod();
      if ($results instanceof Doctrine_Query)
      {
        $objects = $results->execute();
      } else if ($results instanceof Doctrine_Collection) {
        $objects = $results;
      } else if ($results instanceof Doctrine_Record) {
        $objects = new Doctrine_Collection($this->getOption('model'));
        $objects[] = $results;
      } else {
        $objects = array();
      }
    }

    $method = $this->getOption('method');
    $keyMethod = $this->getOption('key_method');

    foreach ($objects as $object)
    {
      try
      {
        $choices[$object->$keyMethod()] = $object->$method();
      } catch (Exception $e) {
        throw $e;
      }
    }

    return $choices;
  }
}