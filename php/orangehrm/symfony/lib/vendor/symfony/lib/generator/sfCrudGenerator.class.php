<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * CRUD generator.
 *
 * This class generates a basic CRUD module.
 *
 * @package    symfony
 * @subpackage generator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfCrudGenerator.class.php 17858 2009-05-01 21:22:50Z FabianLange $
 */
abstract class sfCrudGenerator extends sfGenerator
{
  protected
    $singularName  = '',
    $pluralName    = '',
    $map           = null,
    $tableMap      = null,
    $primaryKey    = array(),
    $className     = '',
    $params        = array();

  /**
   * Generates classes and templates in cache.
   *
   * @param array $params The parameters
   *
   * @return string The data to put in configuration cache
   */
  public function generate($params = array())
  {
    $this->params = $params;

    $required_parameters = array('model_class', 'moduleName');
    foreach ($required_parameters as $entry)
    {
      if (!isset($this->params[$entry]))
      {
        throw new sfParseException(sprintf('You must specify a "%s".', $entry));
      }
    }

    $modelClass = $this->params['model_class'];

    if (!class_exists($modelClass))
    {
      throw new sfInitializationException(sprintf('Unable to scaffold nonexistent model "%s".', $modelClass));
    }

    $this->setScaffoldingClassName($modelClass);

    // generated module name
    $this->setGeneratedModuleName('auto'.ucfirst($this->params['moduleName']));
    $this->setModuleName($this->params['moduleName']);

    // get some model metadata
    $this->loadMapBuilderClasses();

    // load all primary keys
    $this->loadPrimaryKeys();

    // theme exists?
    $theme = isset($this->params['theme']) ? $this->params['theme'] : 'default';
    $themeDir = $this->generatorManager->getConfiguration()->getGeneratorTemplate($this->getGeneratorClass(), $theme, '');
    if (!is_dir($themeDir))
    {
      throw new sfConfigurationException(sprintf('The theme "%s" does not exist.', $theme));
    }

    $this->setTheme($theme);
    $files = sfFinder::type('file')->relative()->in($themeDir);

    $this->generatePhpFiles($this->generatedModuleName, $files);

    // require generated action class
    $data = "require_once(sfConfig::get('sf_module_cache_dir').'/".$this->generatedModuleName."/actions/actions.class.php');\n";

    return $data;
  }

  /**
   * Returns PHP code for primary keys parameters.
   *
   * @param integer $indent The indentation value
   * @param string  $callee The function to call
   *
   * @return string The PHP code
   */
  public function getRetrieveByPkParamsForAction($indent, $callee = '$this->getRequestParameter')
  {
    $params = array();
    foreach ($this->getPrimaryKey() as $pk)
    {
      $params[] = "$callee('".sfInflector::underscore($pk->getPhpName())."')";
    }

    return implode(",\n".str_repeat(' ', max(0, $indent - strlen($this->singularName.$this->className))), $params);
  }

  /**
   * Returns PHP code for primary keys parameters.
   *
   * @param integer $indent The indentation value
   * @param string  $prefix No effect at this time
   *
   * @return string The PHP code
   */
  public function getRetrieveByPkParamsForEdit($indent, $prefix)
  {
    $params = array();
    foreach ($this->getPrimaryKey() as $pk)
    {
      $params[] = sprintf("\$request->getParameter('%s')", sfInflector::underscore($pk->getPhpName()));
    }

    return implode(",\n".str_repeat(' ', max(0, $indent - strlen($this->singularName.$this->className))), $params);
  }

  /**
   * Returns PHP code for getOrCreate() parameters.
   *
   * @return string The PHP code
   */
  public function getMethodParamsForGetOrCreate()
  {
    $method_params = array();
    foreach ($this->getPrimaryKey() as $pk)
    {
      $fieldName       = sfInflector::underscore($pk->getPhpName());
      $method_params[] = "\$$fieldName = '$fieldName'";
    }

    return implode(', ', $method_params);
  }

  /**
   * Returns PHP code for getOrCreate() promary keys condition.
   *
   * @param boolean $fieldNameAsArgument true if we pass the field name as an argument, false otherwise
   *
   * @return string The PHP code
   */
  public function getTestPksForGetOrCreate($fieldNameAsArgument = true)
  {
    $test_pks = array();
    foreach ($this->getPrimaryKey() as $pk)
    {
      $fieldName  = sfInflector::underscore($pk->getPhpName());
      // 2 checks needed here, as '0' is a vaild PK.
      $test_pks[] = sprintf("\$this->getRequestParameter(%s) === ''", $fieldNameAsArgument ? "\$$fieldName" : "'".$fieldName."'");
      $test_pks[] = sprintf("\$this->getRequestParameter(%s) === null", $fieldNameAsArgument ? "\$$fieldName" : "'".$fieldName."'");
    }

    return implode("\n     || ", $test_pks);
  }

  /**
   * Returns PHP code for primary keys parameters used in getOrCreate() method.
   *
   * @return string The PHP code
   */
  public function getRetrieveByPkParamsForGetOrCreate()
  {
    $retrieve_params = array();
    foreach ($this->getPrimaryKey() as $pk)
    {
      $fieldName         = sfInflector::underscore($pk->getPhpName());
      $retrieve_params[] = "\$this->getRequestParameter(\$$fieldName)";
    }

    return implode(",\n".str_repeat(' ', max(0, 45 - strlen($this->singularName.$this->className))), $retrieve_params);
  }

  /**
   * Gets the table map for the current model class.
   *
   * @return TableMap A TableMap instance
   */
  public function getTableMap()
  {
    return $this->tableMap;
  }

  /**
   * Sets the class name to use for scaffolding
   *
   * @param string $className class name
   */
  protected function setScaffoldingClassName($className)
  {
    $this->singularName = isset($this->params['singular']) ? $this->params['singular'] : sfInflector::underscore($className);
    $this->pluralName   = isset($this->params['plural']) ? $this->params['plural'] : $this->singularName.'s';
    $this->className    = $className;
  }

  /**
   * Gets the singular name for current scaffolding class.
   *
   * @return string
   */
  public function getSingularName()
  {
    return $this->singularName;
  }

  /**
   * Gets the plural name for current scaffolding class.
   *
   * @return string
   */
  public function getPluralName()
  {
    return $this->pluralName;
  }

  /**
   * Gets the class name for current scaffolding class.
   *
   * @return string
   */
  public function getClassName()
  {
    return $this->className;
  }

  /**
   * Gets the primary key name.
   *
   * @return string
   */
  public function getPrimaryKey()
  {
    return $this->primaryKey;
  }

  /**
   * Gets the Map object.
   *
   * @return object
   */
  public function getMap()
  {
    return $this->map;
  }

  /**
   * Returns PHP code to add to a URL for primary keys.
   *
   * @param string $prefix The prefix value
   *
   * @return string PHP code
   */
  public function getPrimaryKeyUrlParams($prefix = '', $full = false)
  {
    $params = array();
    foreach ($this->getPrimaryKey() as $pk)
    {
      $phpName   = $pk->getPhpName();
      $fieldName = sfInflector::underscore($phpName);
      if ($full)
      {
        $params[]  = "$fieldName='.".$prefix.'->'.$this->getColumnGetter($pk, false).'()';
      }
      else
      {
        $params[]  = "$fieldName='.".$this->getColumnGetter($pk, true, $prefix);
      }
    }

    return implode(".'&", $params);
  }

  /**
   * Gets PHP code for primary key condition.
   *
   * @param string $prefix The prefix value
   *
   * @return string PHP code
   */
  public function getPrimaryKeyIsSet($prefix = '')
  {
    $params = array();
    foreach ($this->getPrimaryKey() as $pk)
    {
      $params[] = $this->getColumnGetter($pk, true, $prefix);
    }

    return implode(' && ', $params);
  }

  /**
   * Gets object tag parameters.
   *
   * @param array $params         An array of parameters
   * @param array $default_params An array of default parameters
   *
   * @return string PHP code
   */
  protected function getObjectTagParams($params, $default_params = array())
  {
    return var_export(array_merge($default_params, $params), true);
  }

  /**
   * Returns HTML code for a column in list mode.
   *
   * @param string $column The column name
   * @param array  $params The parameters
   *
   * @return string HTML code
   */
  public function getColumnListTag($column, $params = array())
  {
    $type = $column->getType();
    
    $columnGetter = $this->getColumnGetter($column, true);

    if ($type == PropelColumnTypes::TIMESTAMP)
    {
      return "format_date($columnGetter, 'f')";
    }
    elseif ($type == PropelColumnTypes::DATE)
    {
      return "format_date($columnGetter, 'D')";
    }
    else
    {
      return "$columnGetter";
    }
  }

  /**
   * Returns HTML code for a column in edit mode.
   *
   * @param string $column The column name
   * @param array  $params The parameters
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
    else if ($type == PropelColumnTypes::LONGVARCHAR)
    {
      return $this->getPHPObjectHelper('textarea_tag', $column, $params, array('size' => '30x3'));
    }
    else
    {
      return $this->getPHPObjectHelper('input_tag', $column, $params, array('disabled' => true));
    }
  }

  /**
   * Loads primary keys.
   *
   * This method is ORM dependant.
   *
   * @throws sfException
   */
  abstract protected function loadPrimaryKeys();

  /**
   * Loads map builder classes.
   *
   * This method is ORM dependant.
   *
   * @throws sfException
   */
  abstract protected function loadMapBuilderClasses();

  /**
   * Generates a PHP call to an object helper.
   *
   * This method is ORM dependant.
   *
   * @param string $helperName  The helper name
   * @param string $column      The column name
   * @param array  $params      An array of parameters
   * @param array  $localParams An array of local parameters
   *
   * @return string PHP code
   */
  abstract function getPHPObjectHelper($helperName, $column, $params, $localParams = array());

  /**
   * Returns the getter either non-developped: 'getFoo' or developped: '$class->getFoo()'.
   *
   * This method is ORM dependant.
   *
   * @param string  $column    The column name
   * @param boolean $developed true if you want developped method names, false otherwise
   * @param string  $prefix    The prefix value
   *
   * @return string PHP code
   */
  abstract function getColumnGetter($column, $developed = false , $prefix = '');

  /*
   * Gets the PHP name of the related class name.
   *
   * Used for foreign keys only; this method should be removed when we use sfAdminColumn instead.
   *
   * This method is ORM dependant.
   *
   * @param string $column The column name
   *
   * @return string The PHP name of the related class name
   */
  abstract function getRelatedClassName($column);
}
