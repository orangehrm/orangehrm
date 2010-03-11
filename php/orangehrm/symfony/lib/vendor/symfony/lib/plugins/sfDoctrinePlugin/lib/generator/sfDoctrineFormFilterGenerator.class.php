<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Doctrine filter form generator.
 *
 * This class generates a Doctrine filter forms.
 *
 * @package    symfony
 * @subpackage generator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfDoctrineFormFilterGenerator.class.php 11675 2008-09-19 15:21:38Z fabien $
 */
class sfDoctrineFormFilterGenerator extends sfDoctrineFormGenerator
{
  /**
   * Initializes the current sfGenerator instance.
   *
   * @param sfGeneratorManager $generatorManager A sfGeneratorManager instance
   */
  public function initialize(sfGeneratorManager $generatorManager)
  {
    parent::initialize($generatorManager);

    $this->setGeneratorClass('sfDoctrineFormFilter');
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

    if (!isset($this->params['filter_dir_name']))
    {
      $this->params['filter_dir_name'] = 'filter';
    }

    $models = $this->loadModels();

    // create the project base class for all forms
    $file = sfConfig::get('sf_lib_dir').'/filter/doctrine/BaseFormFilterDoctrine.class.php';
    if (!file_exists($file))
    {
      if (!is_dir(sfConfig::get('sf_lib_dir').'/filter/doctrine/base'))
      {
        mkdir(sfConfig::get('sf_lib_dir').'/filter/doctrine/base', 0777, true);
      }

      file_put_contents($file, $this->evalTemplate('sfDoctrineFormFilterBaseTemplate.php'));
    }

    $pluginPaths = $this->generatorManager->getConfiguration()->getAllPluginPaths();

    // create a form class for every Doctrine class
    foreach ($models as $model)
    {
      $this->table = Doctrine::getTable($model);
      $this->modelName = $model;

      $baseDir = sfConfig::get('sf_lib_dir') . '/filter/doctrine';

      $isPluginModel = $this->isPluginModel($model);
      if ($isPluginModel)
      {
        $pluginName = $this->getPluginNameForModel($model);
        $baseDir .= '/' . $pluginName;
      }

      if (!is_dir($baseDir.'/base'))
      {
        mkdir($baseDir.'/base', 0777, true);
      }

      file_put_contents($baseDir.'/base/Base'.$model.'FormFilter.class.php', $this->evalTemplate('sfDoctrineFormFilterGeneratedTemplate.php'));
      if ($isPluginModel)
      {
        $pluginBaseDir = $pluginPaths[$pluginName].'/lib/filter/doctrine';
        if (!file_exists($classFile = $pluginBaseDir.'/Plugin'.$model.'FormFilter.class.php'))
        {
            if (!is_dir($pluginBaseDir))
            {
              mkdir($pluginBaseDir, 0777, true);
            }
            file_put_contents($classFile, $this->evalTemplate('sfDoctrineFormFilterPluginTemplate.php'));
        }
      }
      if (!file_exists($classFile = $baseDir.'/'.$model.'FormFilter.class.php'))
      {
        if ($isPluginModel)
        {
           file_put_contents($classFile, $this->evalTemplate('sfDoctrinePluginFormFilterTemplate.php'));
        } else {
           file_put_contents($classFile, $this->evalTemplate('sfDoctrineFormFilterTemplate.php'));
        }
      }
    }
  }

  /**
   * Returns a sfWidgetForm class name for a given column.
   *
   * @param  sfDoctrineColumn $column
   * @return string    The name of a subclass of sfWidgetForm
   */
  public function getWidgetClassForColumn($column)
  {
    switch ($column->getDoctrineType())
    {
      case 'boolean':
        $name = 'Choice';
        break;
      case 'date':
      case 'datetime':
      case 'timestamp':
        $name = 'FilterDate';
        break;
      case 'enum':
        $name = 'Choice';
        break;
      default:
        $name = 'FilterInput';
    }

    if ($column->isForeignKey())
    {
      $name = 'DoctrineChoice';
    }

    return sprintf('sfWidgetForm%s', $name);
  }

  /**
   * Returns a PHP string representing options to pass to a widget for a given column.
   *
   * @param  sfDoctrineColumn $column
   * @return string    The options to pass to the widget as a PHP string
   */
  public function getWidgetOptionsForColumn($column)
  {
    $options = array();

    $withEmpty = sprintf('\'with_empty\' => %s', $column->isNotNull() ? 'false' : 'true');
    switch ($column->getDoctrineType())
    {
      case 'boolean':
        $options[] = "'choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')";
        break;
      case 'date':
      case 'datetime':
      case 'timestamp':
        $options[] = "'from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate()";
        $options[] = $withEmpty;
        break;
      case 'enum':
        $values = array('' => '');
        $values = array_merge($values, $column['values']);
        $values = array_combine($values, $values);
        $options[] = "'choices' => " . str_replace("\n", '', $this->arrayExport($values));
        break;
    }

    if ($column->isForeignKey())
    {
      $options[] = sprintf('\'model\' => \'%s\', \'add_empty\' => true', $column->getForeignTable()->getOption('name'));
    }

    return count($options) ? sprintf('array(%s)', implode(', ', $options)) : '';
  }

  /**
   * Returns a sfValidator class name for a given column.
   *
   * @param  sfDoctrineColumn $column
   * @return string    The name of a subclass of sfValidator
   */
  public function getValidatorClassForColumn($column)
  {
    switch ($column->getDoctrineType())
    {
      case 'boolean':
        $name = 'Choice';
        break;
      case 'float':
      case 'decimal':
        $name = 'Number';
        break;
      case 'integer':
        $name = 'Integer';
        break;
      case 'date':
      case 'datetime':
      case 'timestamp':
        $name = 'DateRange';
        break;
      case 'enum':
        $name = 'Choice';
        break;
      default:
        $name = 'Pass';
    }

    if ($column->isPrimarykey() || $column->isForeignKey())
    {
      $name = 'DoctrineChoice';
    }

    return sprintf('sfValidator%s', $name);
  }

  /**
   * Returns a PHP string representing options to pass to a validator for a given column.
   *
   * @param  sfDoctrineColumn $column
   * @return string    The options to pass to the validator as a PHP string
   */
  public function getValidatorOptionsForColumn($column)
  {
    $options = array('\'required\' => false');

    if ($column->isForeignKey())
    {
      $columns = $column->getForeignTable()->getColumns();
      foreach ($columns as $name => $col)
      {
        if (isset($col['primary']) && $col['primary'])
        {
          break;
        }
      }

      $options[] = sprintf('\'model\' => \'%s\', \'column\' => \'%s\'', $column->getForeignTable()->getOption('name'), $column->getForeignTable()->getFieldName($name));
    }
    else if ($column->isPrimaryKey())
    {
      $options[] = sprintf('\'model\' => \'%s\', \'column\' => \'%s\'', $this->table->getOption('name'), $this->table->getFieldName($columnName));
    }
    else
    {
      switch ($column->getDoctrineType())
      {
        case 'boolean':
          $options[] = "'choices' => array('', 1, 0)";
          break;
        case 'date':
        case 'datetime':
        case 'timestamp':
          $options[] = "'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false))";
          break;
        case 'enum':
          $values = array_combine($column['values'], $column['values']);
          $options[] = "'choices' => " . str_replace("\n", '', $this->arrayExport($values));
          break;
      }
    }

    return count($options) ? sprintf('array(%s)', implode(', ', $options)) : '';
  }

  public function getValidatorForColumn($column)
  {
    $format = 'new %s(%s)';
    if (in_array($class = $this->getValidatorClassForColumn($column), array('sfValidatorInteger', 'sfValidatorNumber')))
    {
      $format = 'new sfValidatorSchemaFilter(\'text\', new %s(%s))';
    }

    return sprintf($format, $class, $this->getValidatorOptionsForColumn($column));
  }

  public function getType($column)
  {
    if ($column->isForeignKey())
    {
      return 'ForeignKey';
    }

    switch ($column->getDoctrineType())
    {
      case 'enum':
        return 'Enum';
      case 'boolean':
        return 'Boolean';
      case 'date':
      case 'datetime':
      case 'timestamp':
        return 'Date';
      case 'integer':
      case 'decimal':
      case 'float':
        return 'Number';
      default:
        return 'Text';
    }
  }

  /**
   * Array export. Export array to formatted php code
   *
   * @param array $values
   * @return string $php
   */
  protected function arrayExport($values)
  {
    $php = var_export($values, true);
    $php = str_replace("\n", '', $php);
    $php = str_replace('array (  ', 'array(', $php);
    $php = str_replace(',)', ')', $php);
    $php = str_replace('  ', ' ', $php);
    return $php;
  }
}