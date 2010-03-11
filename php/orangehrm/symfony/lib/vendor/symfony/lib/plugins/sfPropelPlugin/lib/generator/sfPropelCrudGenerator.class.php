<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Propel CRUD generator.
 *
 * This class generates a basic CRUD module with propel.
 *
 * @package    symfony
 * @subpackage propel
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfPropelCrudGenerator.class.php 13927 2008-12-10 21:47:32Z FabianLange $
 */
class sfPropelCrudGenerator extends sfAdminGenerator
{
  protected
    $peerClassName = '';

  /**
   * Initializes the current sfGenerator instance.
   *
   * @param sfGeneratorManager $generatorManager A sfGeneratorManager instance
   */
  public function initialize(sfGeneratorManager $generatorManager)
  {
    parent::initialize($generatorManager);

    $this->setGeneratorClass('sfPropelCrud');
  }

  /**
   * Sets the class name to use for scaffolding
   *
   * @param string $className class name
   */
  protected function setScaffoldingClassName($className)
  {
    parent::setScaffoldingClassName($className);

    $this->peerClassName = constant($className.'::PEER');
  }

  /**
   * Loads primary keys.
   *
   * This method is ORM dependant.
   *
   * @throws sfException
   */
  protected function loadPrimaryKeys()
  {
    foreach ($this->tableMap->getColumns() as $column)
    {
      if ($column->isPrimaryKey())
      {
        $this->primaryKey[] = $column;
      }
    }

    if (!count($this->primaryKey))
    {
      throw new sfException(sprintf('Cannot generate a module for a model without a primary key (%s)', $this->className));
    }
  }

  /**
   * Loads map builder classes.
   *
   * This method is ORM dependant.
   *
   * @throws sfException
   */
  protected function loadMapBuilderClasses()
  {
    // we must load all map builder classes to be able to deal with foreign keys (cf. editSuccess.php template)
    $classes = sfFinder::type('file')->name('*MapBuilder.php')->in($this->generatorManager->getConfiguration()->getModelDirs());
    foreach ($classes as $class)
    {
      $omClass = basename($class, 'MapBuilder.php');
      if (class_exists($omClass) && is_subclass_of($omClass, 'BaseObject'))
      {
        $class_map_builder = basename($class, '.php');
        $maps[$class_map_builder] = new $class_map_builder();
        if (!$maps[$class_map_builder]->isBuilt())
        {
          $maps[$class_map_builder]->doBuild();
        }

        if ($this->className == $omClass)
        {
          $this->map = $maps[$class_map_builder];
        }
      }
    }
    if (!$this->map)
    {
      throw new sfException(sprintf('The model class "%s" does not exist.', $this->className));
    }

    $this->tableMap = $this->map->getDatabaseMap()->getTable(constant($this->peerClassName.'::TABLE_NAME'));
  }

  /**
   * Generates a PHP call to an object helper.
   *
   * @param string $helperName  The helper name
   * @param string $column      The column name
   * @param array  $params      An array of parameters
   * @param array  $localParams An array of local parameters
   *
   * @return string PHP code
   */
  function getPHPObjectHelper($helperName, $column, $params, $localParams = array())
  {
    $params = $this->getObjectTagParams($params, $localParams);

    return sprintf('object_%s($%s, \'%s\', %s)', $helperName, $this->getSingularName(), $this->getColumnGetter($column, false), $params);
  }

  /**
   * Returns the getter either non-developped: 'getFoo' or developped: '$class->getFoo()'.
   *
   * @param string  $column     The column name
   * @param boolean $developed  true if you want developped method names, false otherwise
   * @param string  $prefix     The prefix value
   *
   * @return string PHP code
   */
  function getColumnGetter($column, $developed = false, $prefix = '')
  {
    $getter = 'get'.$column->getPhpName();
    if ($developed)
    {
      $getter = sprintf('$%s%s->%s()', $prefix, $this->getSingularName(), $getter);
    }

    return $getter;
  }

  /*
   * Gets the PHP name of the related class name.
   *
   * Used for foreign keys only; this method should be removed when we use sfAdminColumn instead.
   *
   * @param string $column  The column name
   *
   * @return string The PHP name of the related class name
   */
  function getRelatedClassName($column)
  {
    $relatedTable = $this->getMap()->getDatabaseMap()->getTable($column->getRelatedTableName());

    return $relatedTable->getPhpName();
  }

  /**
   * Returns HTML code for a column in edit mode.
   *
   * @param string  The column name
   * @param array   The parameters
   *
   * @return string HTML code
   */
  public function getCrudColumnEditTag($column, $params = array())
  {
    $type = $column->getType();

    if ($column->isForeignKey())
    {
      if (!$column->isNotNull() && !isset($params['include_blank']))
      {
        $params['include_blank'] = true;
      }

      return $this->getPHPObjectHelper('select_tag', $column, $params, array('related_class' => $this->getRelatedClassName($column)));
    }
    else if ($type == PropelColumnTypes::DATE)
    {
      // rich=false not yet implemented
      return $this->getPHPObjectHelper('input_date_tag', $column, $params, array('rich' => true));
    }
    else if ($type == PropelColumnTypes::TIMESTAMP)
    {
      // rich=false not yet implemented
      return $this->getPHPObjectHelper('input_date_tag', $column, $params, array('rich' => true, 'withtime' => true));
    }
    else if ($type == PropelColumnTypes::BOOLEAN)
    {
      return $this->getPHPObjectHelper('checkbox_tag', $column, $params);
    }
    else if ($type == PropelColumnTypes::CHAR || $type == PropelColumnTypes::VARCHAR)
    {
      $size = ($column->getSize() > 20 ? ($column->getSize() < 80 ? $column->getSize() : 80) : 20);
      return $this->getPHPObjectHelper('input_tag', $column, $params, array('size' => $size));
    }
    else if ($type == PropelColumnTypes::INTEGER || $type == PropelColumnTypes::TINYINT || $type == PropelColumnTypes::SMALLINT || $type == PropelColumnTypes::BIGINT)
    {
      return $this->getPHPObjectHelper('input_tag', $column, $params, array('size' => 7));
    }
    else if ($type == PropelColumnTypes::FLOAT || $type == PropelColumnTypes::DOUBLE || $type == PropelColumnTypes::DECIMAL || $type == PropelColumnTypes::NUMERIC || $type == PropelColumnTypes::REAL)
    {
      return $this->getPHPObjectHelper('input_tag', $column, $params, array('size' => 7));
    }
    else if ($type == PropelColumnTypes::LONGVARCHAR || $type == PropelColumnTypes::CLOB)
    {
      return $this->getPHPObjectHelper('textarea_tag', $column, $params, array('size' => '30x3'));
    }
    else
    {
      return $this->getPHPObjectHelper('input_tag', $column, $params, array('disabled' => true));
    }
  }

  /**
   * Returns HTML code for a column in filter mode.
   *
   * @param string  The column name
   * @param array   The parameters
   *
   * @return string HTML code
   */
  public function getColumnFilterTag($column, $params = array())
  {
    $user_params = $this->getParameterValue('list.fields.'.$column->getName().'.params');
    $user_params = is_array($user_params) ? $user_params : sfToolkit::stringToArray($user_params);
    $params      = $user_params ? array_merge($params, $user_params) : $params;

    if ($column->isComponent())
    {
      return "get_component('".$this->getModuleName()."', '".$column->getName()."', array('type' => 'filter', 'filters' => \$filters))";
    }
    else if ($column->isPartial())
    {
      return "get_partial('".$column->getName()."', array('type' => 'filter', 'filters' => \$filters))";
    }

    $type = $column->getType();

    $default_value = "isset(\$filters['".$column->getName()."']) ? \$filters['".$column->getName()."'] : null";
    $unquotedName = 'filters['.$column->getName().']';
    $name = "'$unquotedName'";

    if ($column->isForeignKey())
    {
      $params = $this->getObjectTagParams($params, array('include_blank' => true, 'related_class'=>$this->getRelatedClassName($column), 'text_method'=>'__toString', 'control_name'=>$unquotedName));
      return "object_select_tag($default_value, null, $params)";

    }
    else if ($type == PropelColumnTypes::DATE)
    {
      // rich=false not yet implemented
      $params = $this->getObjectTagParams($params, array('rich' => true, 'calendar_button_img' => sfConfig::get('sf_admin_web_dir').'/images/date.png'));
      return "input_date_range_tag($name, $default_value, $params)";
    }
    else if ($type == PropelColumnTypes::TIMESTAMP)
    {
      // rich=false not yet implemented
      $params = $this->getObjectTagParams($params, array('rich' => true, 'withtime' => true, 'calendar_button_img' => sfConfig::get('sf_admin_web_dir').'/images/date.png'));
      return "input_date_range_tag($name, $default_value, $params)";
    }
    else if ($type == PropelColumnTypes::BOOLEAN)
    {
      $defaultIncludeCustom = '__("yes or no")';

      $option_params = $this->getObjectTagParams($params, array('include_custom' => $defaultIncludeCustom));
      $params = $this->getObjectTagParams($params);

      // little hack
      $option_params = preg_replace("/'".preg_quote($defaultIncludeCustom)."'/", $defaultIncludeCustom, $option_params);

      $options = "options_for_select(array(1 => __('yes'), 0 => __('no')), $default_value, $option_params)";

      return "select_tag($name, $options, $params)";
    }
    else if ($type == PropelColumnTypes::CHAR || $type == PropelColumnTypes::VARCHAR || $type == PropelColumnTypes::LONGVARCHAR || $type == PropelColumnTypes::CLOB)
    {
      $size = ($column->getSize() < 15 ? $column->getSize() : 15);
      $params = $this->getObjectTagParams($params, array('size' => $size));
      return "input_tag($name, $default_value, $params)";
    }
    else if ($type == PropelColumnTypes::INTEGER || $type == PropelColumnTypes::TINYINT || $type == PropelColumnTypes::SMALLINT || $type == PropelColumnTypes::BIGINT)
    {
      $params = $this->getObjectTagParams($params, array('size' => 7));
      return "input_tag($name, $default_value, $params)";
    }
    else if ($type == PropelColumnTypes::FLOAT || $type == PropelColumnTypes::DOUBLE || $type == PropelColumnTypes::DECIMAL || $type == PropelColumnTypes::NUMERIC || $type == PropelColumnTypes::REAL)
    {
      $params = $this->getObjectTagParams($params, array('size' => 7));
      return "input_tag($name, $default_value, $params)";
    }
    else
    {
      $params = $this->getObjectTagParams($params, array('disabled' => true));
      return "input_tag($name, $default_value, $params)";
    }
  }

  /**
   * Returns HTML code for a column in edit mode.
   *
   * @param string  The column name
   * @param array   The parameters
   *
   * @return string HTML code
   */
  public function getColumnEditTag($column, $params = array())
  {
    // user defined parameters
    $user_params = $this->getParameterValue('edit.fields.'.$column->getName().'.params');
    $user_params = is_array($user_params) ? $user_params : sfToolkit::stringToArray($user_params);
    $params      = $user_params ? array_merge($params, $user_params) : $params;

    if ($column->isComponent())
    {
      return "get_component('".$this->getModuleName()."', '".$column->getName()."', array('type' => 'edit', '{$this->getSingularName()}' => \${$this->getSingularName()}))";
    }
    else if ($column->isPartial())
    {
      return "get_partial('".$column->getName()."', array('type' => 'edit', '{$this->getSingularName()}' => \${$this->getSingularName()}))";
    }

    // default control name
    $params = array_merge(array('control_name' => $this->getSingularName().'['.$column->getName().']'), $params);

    // default parameter values
    $type = $column->getType();
    if ($type == PropelColumnTypes::DATE)
    {
      $params = array_merge(array('rich' => true, 'calendar_button_img' => sfConfig::get('sf_admin_web_dir').'/images/date.png'), $params);
    }
    else if ($type == PropelColumnTypes::TIMESTAMP)
    {
      $params = array_merge(array('rich' => true, 'withtime' => true, 'calendar_button_img' => sfConfig::get('sf_admin_web_dir').'/images/date.png'), $params);
    }

    // user sets a specific tag to use
    if ($inputType = $this->getParameterValue('edit.fields.'.$column->getName().'.type'))
    {
      if ($inputType == 'plain')
      {
        return $this->getColumnListTag($column, $params);
      }
      else
      {
        return $this->getPHPObjectHelper($inputType, $column, $params);
      }
    }

    // guess the best tag to use with column type
    return $this->getCrudColumnEditTag($column, $params);
  }

  /**
   * Returns HTML code for a column in list mode.
   *
   * @param string  The column name
   * @param array   The parameters
   *
   * @return string HTML code
   */
  public function getColumnListTag($column, $params = array())
  {
    $user_params = $this->getParameterValue('list.fields.'.$column->getName().'.params');
    $user_params = is_array($user_params) ? $user_params : sfToolkit::stringToArray($user_params);
    $params      = $user_params ? array_merge($params, $user_params) : $params;

    $type = $column->getType();

    $columnGetter = $this->getColumnGetter($column, true);

    if ($column->isComponent())
    {
      return "get_component('".$this->getModuleName()."', '".$column->getName()."', array('type' => 'list', '{$this->getSingularName()}' => \${$this->getSingularName()}))";
    }
    else if ($column->isPartial())
    {
      return "get_partial('".$column->getName()."', array('type' => 'list', '{$this->getSingularName()}' => \${$this->getSingularName()}))";
    }
    else if ($type == PropelColumnTypes::DATE || $type == PropelColumnTypes::TIMESTAMP)
    {
      $format = isset($params['date_format']) ? $params['date_format'] : ($type == PropelColumnTypes::DATE ? 'D' : 'f');
      return "($columnGetter !== null && $columnGetter !== '') ? format_date($columnGetter, \"$format\") : ''";
    }
    elseif ($type == PropelColumnTypes::BOOLEAN)
    {
      return "$columnGetter ? image_tag(sfConfig::get('sf_admin_web_dir').'/images/tick.png') : '&nbsp;'";
    }
    else
    {
      return "$columnGetter";
    }
  }

  /**
   * Gets the Peer class name.
   *
   * @return string
   */
  public function getPeerClassName()
  {
    return $this->peerClassName;
  }

  /**
   * Returns the URL for a given action.
   *
   * @return string The URL related to a given action
   */
  public function getUrlForAction($action)
  {
    if (isset($this->params['route_prefix']))
    {
      return 'list' == $action ? $this->params['route_prefix'] : $this->params['route_prefix'].'_'.$action;
    }
    else
    {
      return $this->getModuleName().'/'.$action;
    }
  }
}
