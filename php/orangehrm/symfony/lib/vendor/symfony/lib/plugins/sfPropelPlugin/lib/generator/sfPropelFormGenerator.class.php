<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Propel form generator.
 *
 * This class generates a Propel forms.
 *
 * @package    symfony
 * @subpackage generator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfPropelFormGenerator.class.php 16976 2009-04-04 12:47:44Z fabien $
 */
class sfPropelFormGenerator extends sfGenerator
{
  protected
    $dbMap = null;

  /**
   * Initializes the current sfGenerator instance.
   *
   * @param sfGeneratorManager $generatorManager A sfGeneratorManager instance
   */
  public function initialize(sfGeneratorManager $generatorManager)
  {
    parent::initialize($generatorManager);

    $this->setGeneratorClass('sfPropelForm');
  }

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

    if (!isset($this->params['connection']))
    {
      throw new sfParseException('You must specify a "connection" parameter.');
    }

    if (!isset($this->params['model_dir_name']))
    {
      $this->params['model_dir_name'] = 'model';
    }

    if (!isset($this->params['form_dir_name']))
    {
      $this->params['form_dir_name'] = 'form';
    }

    $this->loadBuilders();

    $this->dbMap = Propel::getDatabaseMap($this->params['connection']);

    // create the project base class for all forms
    $file = sfConfig::get('sf_lib_dir').'/form/BaseFormPropel.class.php';
    if (!file_exists($file))
    {
      if (!is_dir(sfConfig::get('sf_lib_dir').'/form/base'))
      {
        mkdir(sfConfig::get('sf_lib_dir').'/form/base', 0777, true);
      }

      file_put_contents($file, $this->evalTemplate('sfPropelFormBaseTemplate.php'));
    }

    // create a form class for every Propel class
    foreach ($this->dbMap->getTables() as $tableName => $table)
    {
      $this->table = $table;

      // find the package to store forms in the same directory as the model classes
      $packages = explode('.', constant(constant($table->getClassname().'::PEER').'::CLASS_DEFAULT'));
      array_pop($packages);
      if (false === $pos = array_search($this->params['model_dir_name'], $packages))
      {
        throw new InvalidArgumentException(sprintf('Unable to find the model dir name (%s) in the package %s.', $this->params['model_dir_name'], constant(constant($table->getClassname().'::PEER').'::CLASS_DEFAULT')));
      }
      $packages[$pos] = $this->params['form_dir_name'];
      $baseDir = sfConfig::get('sf_root_dir').'/'.implode(DIRECTORY_SEPARATOR, $packages);

      if (!is_dir($baseDir.'/base'))
      {
        mkdir($baseDir.'/base', 0777, true);
      }

      file_put_contents($baseDir.'/base/Base'.$table->getClassname().'Form.class.php', $this->evalTemplate('sfPropelFormGeneratedTemplate.php'));
      if (!file_exists($classFile = $baseDir.'/'.$table->getClassname().'Form.class.php'))
      {
        file_put_contents($classFile, $this->evalTemplate('sfPropelFormTemplate.php'));
      }
    }
  }

  /**
   * Returns an array of tables that represents a many to many relationship.
   *
   * A table is considered to be a m2m table if it has 2 foreign keys that are also primary keys.
   *
   * @return array An array of tables.
   */
  public function getManyToManyTables()
  {
    $tables = array();

    // go through all tables to find m2m relationships
    foreach ($this->dbMap->getTables() as $tableName => $table)
    {
      foreach ($table->getColumns() as $column)
      {
        if ($column->isForeignKey() && $column->isPrimaryKey() && $this->table->getClassname() == $this->getForeignTable($column)->getClassname())
        {
          // we have a m2m relationship
          // find the other primary key
          foreach ($table->getColumns() as $relatedColumn)
          {
            if ($relatedColumn->isForeignKey() && $relatedColumn->isPrimaryKey() && $this->table->getClassname() != $this->getForeignTable($relatedColumn)->getClassname())
            {
              // we have the related table
              $tables[] = array(
                'middleTable'   => $table,
                'relatedTable'  => $this->getForeignTable($relatedColumn),
                'column'        => $column,
                'relatedColumn' => $relatedColumn,
              );

              break 2;
            }
          }
        }
      }
    }

    return $tables;
  }

  /**
   * Returns PHP names for all foreign keys of the current table.
   *
   * This method does not returns foreign keys that are also primary keys.
   *
   * @return array An array composed of:
   *                 * The foreign table PHP name
   *                 * The foreign key PHP name
   *                 * A Boolean to indicate whether the column is required or not
   *                 * A Boolean to indicate whether the column is a many to many relationship or not
   */
  public function getForeignKeyNames()
  {
    $names = array();
    foreach ($this->table->getColumns() as $column)
    {
      if (!$column->isPrimaryKey() && $column->isForeignKey())
      {
        $names[] = array($this->getForeignTable($column)->getClassname(), $column->getPhpName(), $column->isNotNull(), false);
      }
    }

    foreach ($this->getManyToManyTables() as $tables)
    {
      $names[] = array($tables['relatedTable']->getClassname(), $tables['middleTable']->getClassname(), false, true);
    }

    return $names;
  }

  /**
   * Returns the first primary key column of the current table.
   *
   * @return ColumnMap A ColumnMap object
   */
  public function getPrimaryKey()
  {
    foreach ($this->table->getColumns() as $column)
    {
      if ($column->isPrimaryKey())
      {
        return $column;
      }
    }
  }

  /**
   * Returns the foreign table associated with a column.
   *
   * @param  ColumnMap $column A ColumnMap object
   *
   * @return TableMap  A TableMap object
   */
  public function getForeignTable(ColumnMap $column)
  {
    return $this->dbMap->getTable($column->getRelatedTableName());
  }

  /**
   * Returns a sfWidgetForm class name for a given column.
   *
   * @param  ColumnMap  $column A ColumnMap object
   *
   * @return string    The name of a subclass of sfWidgetForm
   */
  public function getWidgetClassForColumn(ColumnMap $column)
  {
    switch ($column->getType())
    {
      case PropelColumnTypes::BOOLEAN:
        $name = 'InputCheckbox';
        break;
      case PropelColumnTypes::CLOB:
      case PropelColumnTypes::LONGVARCHAR:
        $name = 'Textarea';
        break;
      case PropelColumnTypes::DATE:
        $name = 'Date';
        break;
      case PropelColumnTypes::TIME:
        $name = 'Time';
        break;
      case PropelColumnTypes::TIMESTAMP:
        $name = 'DateTime';
        break;
      default:
        $name = 'Input';
    }

    if ($column->isPrimaryKey())
    {
      $name = 'InputHidden';
    }
    else if ($column->isForeignKey())
    {
      $name = 'PropelChoice';
    }

    return sprintf('sfWidgetForm%s', $name);
  }

  /**
   * Returns a PHP string representing options to pass to a widget for a given column.
   *
   * @param  ColumnMap $column  A ColumnMap object
   *
   * @return string    The options to pass to the widget as a PHP string
   */
  public function getWidgetOptionsForColumn(ColumnMap $column)
  {
    $options = array();

    if (!$column->isPrimaryKey() && $column->isForeignKey())
    {
      $options[] = sprintf('\'model\' => \'%s\', \'add_empty\' => %s', $this->getForeignTable($column)->getClassname(), $column->isNotNull() ? 'false' : 'true');

      $refColumn = $this->getForeignTable($column)->getColumn($column->getRelatedColumnName());
      if (!$refColumn->isPrimaryKey())
      {
        $options[] = sprintf('\'key_method\' => \'get%s\'', $refColumn->getPhpName());
      }
    }

    return count($options) ? sprintf('array(%s)', implode(', ', $options)) : '';
  }

  /**
   * Returns a sfValidator class name for a given column.
   *
   * @param  ColumnMap $column  A ColumnMap object
   *
   * @return string    The name of a subclass of sfValidator
   */
  public function getValidatorClassForColumn(ColumnMap $column)
  {
    switch ($column->getType())
    {
      case PropelColumnTypes::BOOLEAN:
        $name = 'Boolean';
        break;
      case PropelColumnTypes::CLOB:
      case PropelColumnTypes::CHAR:
      case PropelColumnTypes::VARCHAR:
      case PropelColumnTypes::LONGVARCHAR:
        $name = 'String';
        break;
      case PropelColumnTypes::DOUBLE:
      case PropelColumnTypes::FLOAT:
      case PropelColumnTypes::NUMERIC:
      case PropelColumnTypes::DECIMAL:
      case PropelColumnTypes::REAL:
        $name = 'Number';
        break;
      case PropelColumnTypes::INTEGER:
      case PropelColumnTypes::SMALLINT:
      case PropelColumnTypes::TINYINT:
      case PropelColumnTypes::BIGINT:
        $name = 'Integer';
        break;
      case PropelColumnTypes::DATE:
        $name = 'Date';
        break;
      case PropelColumnTypes::TIME:
        $name = 'Time';
        break;
      case PropelColumnTypes::TIMESTAMP:
        $name = 'DateTime';
        break;
      default:
        $name = 'Pass';
    }

    if ($column->isPrimaryKey() || $column->isForeignKey())
    {
      $name = 'PropelChoice';
    }

    return sprintf('sfValidator%s', $name);
  }

  /**
   * Returns a PHP string representing options to pass to a validator for a given column.
   *
   * @param  ColumnMap $column  A ColumnMap object
   *
   * @return string    The options to pass to the validator as a PHP string
   */
  public function getValidatorOptionsForColumn(ColumnMap $column)
  {
    $options = array();

    if ($column->isForeignKey())
    {
      $options[] = sprintf('\'model\' => \'%s\', \'column\' => \'%s\'', $this->getForeignTable($column)->getClassname(), $this->translateColumnName($column, true));
    }
    else if ($column->isPrimaryKey())
    {
      $options[] = sprintf('\'model\' => \'%s\', \'column\' => \'%s\'', $column->getTable()->getClassname(), $this->translateColumnName($column));
    }
    else
    {
      switch ($column->getType())
      {
        case PropelColumnTypes::CLOB:
        case PropelColumnTypes::CHAR:
        case PropelColumnTypes::VARCHAR:
        case PropelColumnTypes::LONGVARCHAR:
          if ($column->getSize())
          {
            $options[] = sprintf('\'max_length\' => %s', $column->getSize());
          }
          break;
      }
    }

    if (!$column->isNotNull() || $column->isPrimaryKey())
    {
      $options[] = '\'required\' => false';
    }

    return count($options) ? sprintf('array(%s)', implode(', ', $options)) : '';
  }

  /**
   * Returns the maximum length for a column name.
   *
   * @return integer The length of the longer column name
   */
  public function getColumnNameMaxLength()
  {
    $max = 0;
    foreach ($this->table->getColumns() as $column)
    {
      if (($m = strlen($column->getName())) > $max)
      {
        $max = $m;
      }
    }

    foreach ($this->getManyToManyTables() as $tables)
    {
      if (($m = strlen($this->underscore($tables['middleTable']->getClassname()).'_list')) > $max)
      {
        $max = $m;
      }
    }

    return $max;
  }

  /**
   * Returns an array of primary key column names.
   *
   * @return array An array of primary key column names
   */
  public function getPrimaryKeyColumNames()
  {
    $pks = array();
    foreach ($this->table->getColumns() as $column)
    {
      if ($column->isPrimaryKey())
      {
        $pks[] = $this->translateColumnName($column);
      }
    }

    return $pks;
  }

  /**
   * Returns a PHP string representation for the array of all primary key column names.
   *
   * @return string A PHP string representation for the array of all primary key column names
   *
   * @see getPrimaryKeyColumNames()
   */
  public function getPrimaryKeyColumNamesAsString()
  {
    return sprintf('array(\'%s\')', implode('\', \'', $this->getPrimaryKeyColumNames()));
  }

  /**
   * Returns true if the current table is internationalized.
   *
   * @return Boolean true if the current table is internationalized, false otherwise
   */
  public function isI18n()
  {
    return method_exists(constant($this->table->getClassname().'::PEER'), 'getI18nModel');
  }

  /**
   * Returns the i18n model name for the current table.
   *
   * @return string The model class name
   */
  public function getI18nModel()
  {
    return call_user_func(array(constant($this->table->getClassname().'::PEER'), 'getI18nModel'));
  }

  public function underscore($name)
  {
    return strtolower(preg_replace(array('/([A-Z]+)([A-Z][a-z])/', '/([a-z\d])([A-Z])/'), '\\1_\\2', $name));
  }

  public function getUniqueColumnNames()
  {
    $uniqueColumns = array();

    foreach (call_user_func(array(constant($this->table->getClassname().'::PEER'), 'getUniqueColumnNames')) as $unique)
    {
      $uniqueColumn = array();
      foreach ($unique as $column)
      {
        $uniqueColumn[] = $this->translateColumnName($this->table->getColumn($column));
      }

      $uniqueColumns[] = $uniqueColumn;
    }

    return $uniqueColumns;
  }

  public function translateColumnName($column, $related = false, $to = BasePeer::TYPE_FIELDNAME)
  {
    $peer = $related ? constant($column->getTable()->getDatabaseMap()->getTable($column->getRelatedTableName())->getPhpName().'::PEER') : constant($column->getTable()->getPhpName().'::PEER');
    $field = $related ? $column->getRelatedName() : $column->getFullyQualifiedName();

    return call_user_func(array($peer, 'translateFieldName'), $field, BasePeer::TYPE_COLNAME, $to);
  }

  /**
   * Loads all Propel builders.
   */
  protected function loadBuilders()
  {
    $classes = sfFinder::type('file')->name('*MapBuilder.php')->in($this->generatorManager->getConfiguration()->getModelDirs());
    foreach ($classes as $class)
    {
      $omClass = basename($class, 'MapBuilder.php');
      if (class_exists($omClass) && is_subclass_of($omClass, 'BaseObject'))
      {
        $class = basename($class, '.php');
        $map = new $class();
        if (!$map->isBuilt())
        {
          $map->doBuild();
        }
      }
    }
  }
}

