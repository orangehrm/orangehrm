<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Model generator.
 *
 * @package    symfony
 * @subpackage generator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfModelGenerator.class.php 25459 2009-12-16 13:08:43Z fabien $
 */
abstract class sfModelGenerator extends sfGenerator
{
  protected
    $configuration = null,
    $primaryKey    = array(),
    $modelClass    = '',
    $params        = array(),
    $config        = array(),
    $formObject    = null;

  /**
   * Generates classes and templates in cache.
   *
   * @param array $params The parameters
   *
   * @return string The data to put in configuration cache
   */
  public function generate($params = array())
  {
    $this->validateParameters($params);

    $this->modelClass = $this->params['model_class'];

    // generated module name
    $this->setModuleName($this->params['moduleName']);
    $this->setGeneratedModuleName('auto'.ucfirst($this->params['moduleName']));

    // theme exists?
    $theme = isset($this->params['theme']) ? $this->params['theme'] : 'default';
    $this->setTheme($theme);
    $themeDir = $this->generatorManager->getConfiguration()->getGeneratorTemplate($this->getGeneratorClass(), $theme, '');
    if (!is_dir($themeDir))
    {
      throw new sfConfigurationException(sprintf('The theme "%s" does not exist.', $theme));
    }

    // configure the model
    $this->configure();

    $this->configuration = $this->loadConfiguration();

    // generate files
    $this->generatePhpFiles($this->generatedModuleName, sfFinder::type('file')->relative()->in($themeDir));

    // move helper file
    if (file_exists($file = $this->generatorManager->getBasePath().'/'.$this->getGeneratedModuleName().'/lib/helper.php'))
    {
      @rename($file, $this->generatorManager->getBasePath().'/'.$this->getGeneratedModuleName().'/lib/Base'.ucfirst($this->moduleName).'GeneratorHelper.class.php');
    }

    return "require_once(sfConfig::get('sf_module_cache_dir').'/".$this->generatedModuleName."/actions/actions.class.php');";
  }

  /**
   * Gets the actions base class for the generated module.
   *
   * @return string The actions base class
   */
  public function getActionsBaseClass()
  {
    return isset($this->params['actions_base_class']) ? $this->params['actions_base_class'] : 'sfActions';
  }

  /**
   * Gets the class name for current model.
   *
   * @return string
   */
  public function getModelClass()
  {
    return $this->modelClass;
  }

  /**
   * Gets the primary key name.
   *
   * @param Boolean $firstOne Whether to return the first PK or not
   *
   * @return array An array of primary keys
   */
  public function getPrimaryKeys($firstOne = false)
  {
    return $firstOne ? $this->primaryKey[0] : $this->primaryKey;
  }

  /**
   * Gets the singular name for current model.
   *
   * @return string
   */
  public function getSingularName()
  {
    return isset($this->params['singular']) ? $this->params['singular'] : sfInflector::underscore($this->getModelClass());
  }

  /**
   * Gets the plural name for current model.
   *
   * @return string
   */
  public function getPluralName()
  {
    return isset($this->params['plural']) ? $this->params['plural'] : $this->getSingularName().'_list';
  }

  /**
   * Gets the i18n catalogue to use for user strings.
   *
   * @return string The i18n catalogue
   */
  public function getI18nCatalogue()
  {
    return isset($this->params['i18n_catalogue']) ? $this->params['i18n_catalogue'] : 'messages';
  }

  /**
   * Returns PHP code for primary keys parameters.
   *
   * @param integer $indent The indentation value
   * @param string  $callee The function to call
   *
   * @return string The PHP code
   */
  public function getRetrieveByPkParamsForAction($indent)
  {
    $params = array();
    foreach ($this->getPrimaryKeys() as $pk)
    {
      $params[] = sprintf("\$request->getParameter('%s')", sfInflector::underscore($pk));
    }

    return implode(",\n".str_repeat(' ', max(0, $indent - strlen($this->getSingularName().$this->modelClass))), $params);
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
    foreach ($this->getPrimaryKeys() as $pk)
    {
      $fieldName = sfInflector::underscore($pk);

      if ($full)
      {
        $params[] = sprintf("%s='.%s->%s()", $fieldName, $prefix, $this->getColumnGetter($fieldName, false));
      }
      else
      {
        $params[] = sprintf("%s='.%s", $fieldName, $this->getColumnGetter($fieldName, true, $prefix));
      }
    }

    return implode(".'&", $params);
  }

  /** 
   * Configures this generator.
   */
  abstract protected function configure();

  abstract public function getType($column);

  abstract public function getAllFieldNames();

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
  abstract public function getColumnGetter($column, $developed = false , $prefix = '');

  abstract public function getManyToManyTables();

  /**
   * Returns HTML code for an action link.
   *
   * @param string  $actionName The action name
   * @param array   $params     The parameters
   * @param boolean $pk_link    Whether to add a primary key link or not
   *
   * @return string HTML code
   */
  public function getLinkToAction($actionName, $params, $pk_link = false)
  {
    $action = isset($params['action']) ? $params['action'] : 'List'.sfInflector::camelize($actionName);

    $url_params = $pk_link ? '?'.$this->getPrimaryKeyUrlParams() : '\'';

    return '[?php echo link_to(__(\''.$params['label'].'\', array(), \''.$this->getI18nCatalogue().'\'), \''.$this->getModuleName().'/'.$action.$url_params.', '.$this->asPhp($params['params']).') ?]';
  }

  /**
   * Wraps content with a credential condition.
   *
   * @param string $content The content
   * @param array  $params  The parameters
   *
   * @return string HTML code
   */
  public function addCredentialCondition($content, $params = array())
  {
    if (isset($params['credentials']))
    {
      $credentials = $this->asPhp($params['credentials']);

      return <<<EOF
[?php if (\$sf_user->hasCredential($credentials)): ?]
$content
[?php endif; ?]

EOF;
    }
    else
    {
      return $content;
    }
  }

  /**
   * Returns HTML code for a field.
   *
   * @param sfModelGeneratorConfigurationField $field The field
   *
   * @return string HTML code
   */
  public function renderField($field)
  {
    $html = $this->getColumnGetter($field->getName(), true);

    if ($renderer = $field->getRenderer())
    {
      $html = sprintf("$html ? call_user_func_array(%s, array_merge(array(%s), %s)) : '&nbsp;'", $this->asPhp($renderer), $html, $this->asPhp($field->getRendererArguments()));
    }
    else if ($field->isComponent())
    {
      return sprintf("get_component('%s', '%s', array('type' => 'list', '%s' => \$%s))", $this->getModuleName(), $field->getName(), $this->getSingularName(), $this->getSingularName());
    }
    else if ($field->isPartial())
    {
      return sprintf("get_partial('%s/%s', array('type' => 'list', '%s' => \$%s))", $this->getModuleName(), $field->getName(), $this->getSingularName(), $this->getSingularName());
    }
    else if ('Date' == $field->getType())
    {
      $html = sprintf("false !== strtotime($html) ? format_date(%s, \"%s\") : '&nbsp;'", $html, $field->getConfig('date_format', 'f'));
    }
    else if ('Boolean' == $field->getType())
    {
      $html = sprintf("get_partial('%s/list_field_boolean', array('value' => %s))", $this->getModuleName(), $html);
    }

    if ($field->isLink())
    {
      $html = sprintf("link_to(%s, '%s', \$%s)", $html, $this->getUrlForAction('edit'), $this->getSingularName());
    }

    return $html;
  }

  /**
   * Wraps a content for I18N.
   *
   * @param string $key The configuration key name
   *
   * @return string HTML code
   */
  public function getI18NString($key)
  {
    $value = $this->configuration->getValue($key, '', true);

    $parts = explode('.', $key);
    $context = $parts[0];

    // find %%xx%% strings
    preg_match_all('/%%([^%]+)%%/', $value, $matches, PREG_PATTERN_ORDER);
    $fields = array();
    foreach ($matches[1] as $name)
    {
      $fields[] = $name;
    }

    $vars = array();
    foreach ($this->configuration->getContextConfiguration($context, $fields) as $field)
    {
      $vars[] = '\'%%'.$field->getName().'%%\' => '.$this->renderField($field);
    }

    return sprintf("__('%s', array(%s), '%s')", $value, implode(', ', $vars), $this->getI18nCatalogue());
  }

  /**
   * Gets the form object
   *
   * @return sfForm
   */
  public function getFormObject()
  {
    if (null === $this->formObject)
    {
      $class = null === $this->configuration ? $this->getModelClass().'Form' : $this->configuration->getFormClass();

      $this->formObject = new $class();
    }

    return $this->formObject;
  }

  /**
   * Gets the HTML to add to the form tag if the form is multipart.
   *
   * @return string
   */
  public function getFormMultipartHtml()
  {
    if (isset($this->params['non_verbose_templates']) && $this->params['non_verbose_templates'])
    {
      return '[?php $form->isMultipart() and print \' enctype="multipart/form-data"\' ?]';
    }
    else
    {
      return $this->getFormObject()->isMultipart() ? ' enctype="multipart/form-data"' : '';
    }
  }

  /**
   * Validates the basic structure of the parameters.
   *
   * @param array $params An array of parameters
   */
  protected function validateParameters($params)
  {
    foreach (array('model_class', 'moduleName') as $key)
    {
      if (!isset($params[$key]))
      {
        throw new sfParseException(sprintf('sfModelGenerator must have a "%s" parameter.', $key));
      }
    }

    if (!class_exists($params['model_class']))
    {
      throw new sfInitializationException(sprintf('Unable to generate a module for non-existent model "%s".', $params['model_class']));
    }

    $this->config = isset($params['config']) ? $params['config'] : array();

    unset($params['config']);
    $this->params = $params;
  }

  /**
   * Loads the configuration for this generated module.
   */
  protected function loadConfiguration()
  {
    try
    {
      $this->generatorManager->getConfiguration()->getGeneratorTemplate($this->getGeneratorClass(), $this->getTheme(), '../parts/configuration.php');
    }
    catch (sfException $e)
    {
      return null;
    }

    $config = $this->getGeneratorManager()->getConfiguration();
    if (!$config instanceof sfApplicationConfiguration)
    {
      throw new LogicException('The sfModelGenerator can only operates with an application configuration.');
    }

    $basePath = $this->getGeneratedModuleName().'/lib/Base'.ucfirst($this->getModuleName()).'GeneratorConfiguration.class.php';
    $this->getGeneratorManager()->save($basePath, $this->evalTemplate('../parts/configuration.php'));

    require_once $this->getGeneratorManager()->getBasePath().'/'.$basePath;

    $class = 'Base'.ucfirst($this->getModuleName()).'GeneratorConfiguration';
    foreach ($config->getLibDirs($this->getModuleName()) as $dir)
    {
      if (!is_file($configuration = $dir.'/'.$this->getModuleName().'GeneratorConfiguration.class.php'))
      {
        continue;
      }

      require_once $configuration;
      $class = $this->getModuleName().'GeneratorConfiguration';
      break;
    }

    // validate configuration
    foreach ($this->config as $context => $value)
    {
      if (!$value)
      {
        continue;
      }

      throw new InvalidArgumentException(sprintf('Your generator configuration contains some errors for the "%s" context. The following configuration cannot be parsed: %s.', $context, $this->asPhp($value)));
    }

    return new $class();
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

  public function asPhp($variable)
  {
    return str_replace(array("\n", 'array ('), array('', 'array('), var_export($variable, true));
  }

  public function escapeString($string)
  {
    return str_replace("'", "\\'", $string);
  }
}
