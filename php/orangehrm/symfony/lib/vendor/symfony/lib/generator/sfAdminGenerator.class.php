<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Admin generator.
 *
 * This class generates an admin module.
 *
 * This class calls two ORM specific methods:
 *   getAllColumns()
 * and
 *   getAdminColumnForField($field, $flag = null)
 *
 * @package    symfony
 * @subpackage generator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfAdminGenerator.class.php 17858 2009-05-01 21:22:50Z FabianLange $
 */
abstract class sfAdminGenerator extends sfCrudGenerator
{
  protected
    $formObject = null,
    $fields = array();

  /**
   * Returns HTML code for a help icon.
   *
   * @param string $column The column name
   * @param string $type   The field type (list, edit)
   *
   * @return string HTML code
   */
  public function getHelpAsIcon($column, $type = '')
  {
    $help = $this->getParameterValue($type.'.fields.'.$column->getName().'.help');
    if ($help)
    {
      return "[?php echo image_tag(sfConfig::get('sf_admin_web_dir').'/images/help.png', array('align' => 'absmiddle', 'alt' => __('".$this->escapeString($help)."'), 'title' => __('".$this->escapeString($help)."'))) ?]";
    }

    return '';
  }

  /**
   * Returns HTML code for a help text.
   *
   * @param string $column The column name
   * @param string $type   The field type (list, edit)
   *
   * @return string HTML code
   */
  public function getHelp($column, $type = '')
  {
    $help = $this->getParameterValue($type.'.fields.'.$column->getName().'.help');
    if ($help)
    {
      return "<div class=\"sf_admin_edit_help\">[?php echo __('".$this->escapeString($help)."') ?]</div>";
    }

    return '';
  }

  /**
   * Returns HTML code for an action button.
   *
   * @param string  $actionName The action name
   * @param array   $params     The parameters
   * @param boolean $pk_link    Whether to add a primary key link or not
   *
   * @return string HTML code
   */
  public function getButtonToAction($actionName, $params, $pk_link = false)
  {
    $params   = (array) $params;
    $options  = isset($params['params']) ? sfToolkit::stringToArray($params['params']) : array();
    $method   = 'button_to';
    $li_class = '';
    $only_for = isset($params['only_for']) ? $params['only_for'] : null;

    // default values
    if ($actionName[0] == '_')
    {
      $actionName     = substr($actionName, 1);
      $default_name   = strtr($actionName, '_', ' ');
      $default_icon   = sfConfig::get('sf_admin_web_dir').'/images/'.$actionName.'_icon.png';
      $default_action = $actionName;
      $default_class  = 'sf_admin_action_'.$actionName;

      if ($actionName == 'save' || $actionName == 'save_and_add' || $actionName == 'save_and_list')
      {
        $method = 'submit_tag';
        $options['name'] = $actionName;
      }

      if ($actionName == 'delete')
      {
        $options['post'] = true;
        if (!isset($options['confirm']))
        {
          $options['confirm'] = 'Are you sure?';
        }

        $li_class = 'float-left';

        $only_for = 'edit';
      }
    }
    else
    {
      $default_name   = strtr($actionName, '_', ' ');
      $default_icon   = sfConfig::get('sf_admin_web_dir').'/images/default_icon.png';
      $default_action = 'List'.sfInflector::camelize($actionName);
      $default_class  = '';
    }

    $name   = isset($params['name']) ? $params['name'] : $default_name;
    $icon   = isset($params['icon']) ? sfToolkit::replaceConstants($params['icon']) : $default_icon;
    $action = isset($params['action']) ? $params['action'] : $default_action;
    $url_params = $pk_link ? '?'.$this->getPrimaryKeyUrlParams() : '\'';

    if (!isset($options['class']))
    {
      if ($default_class)
      {
        $options['class'] = $default_class;
      }
      else
      {
        $options['style'] = 'background: #ffc url('.$icon.') no-repeat 3px 2px';
      }
    }

    $li_class = $li_class ? ' class="'.$li_class.'"' : '';

    $html = '<li'.$li_class.'>';

    if ($only_for == 'edit')
    {
      $html .= '[?php if ('.$this->getPrimaryKeyIsSet().'): ?]'."\n";
    }
    else if ($only_for == 'create')
    {
      $html .= '[?php if (!'.$this->getPrimaryKeyIsSet().'): ?]'."\n";
    }
    else if ($only_for !== null)
    {
      throw new sfConfigurationException(sprintf('The "only_for" parameter can only takes "create" or "edit" as argument ("%s").', $only_for));
    }

    if ($method == 'submit_tag')
    {
      $html .= '[?php echo submit_tag(__(\''.$name.'\'), '.var_export($options, true).') ?]';
    }
    else
    {
      $phpOptions = var_export($options, true);

      // little hack
      $phpOptions = preg_replace("/'confirm' => '(.+?)(?<!\\\)'/", '\'confirm\' => __(\'$1\')', $phpOptions);

      $html .= '[?php echo button_to(__(\''.$name.'\'), \''.$this->getModuleName().'/'.$action.$url_params.', '.$phpOptions.') ?]';
    }

    if ($only_for !== null)
    {
      $html .= '[?php endif; ?]'."\n";
    }

    $html .= '</li>'."\n";

    return $html;
  }

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
    $options = isset($params['params']) ? sfToolkit::stringToArray($params['params']) : array();

    // default values
    if ($actionName[0] == '_')
    {
      $actionName = substr($actionName, 1);
      $name       = $actionName;
      $icon       = sfConfig::get('sf_admin_web_dir').'/images/'.$actionName.'_icon.png';
      $action     = $actionName;

      if ($actionName == 'delete')
      {
        $options['post'] = true;
        if (!isset($options['confirm']))
        {
          $options['confirm'] = 'Are you sure?';
        }
      }
    }
    else
    {
      $name   = isset($params['name']) ? $params['name'] : $actionName;
      $icon   = isset($params['icon']) ? sfToolkit::replaceConstants($params['icon']) : sfConfig::get('sf_admin_web_dir').'/images/default_icon.png';
      $action = isset($params['action']) ? $params['action'] : 'List'.sfInflector::camelize($actionName);
    }

    $url_params = $pk_link ? '?'.$this->getPrimaryKeyUrlParams() : '\'';

    $phpOptions = var_export($options, true);

    // little hack
    $phpOptions = preg_replace("/'confirm' => '(.+?)(?<!\\\)'/", '\'confirm\' => __(\'$1\')', $phpOptions);

    return '<li>[?php echo link_to(image_tag(\''.$icon.'\', array(\'alt\' => __(\''.$name.'\'), \'title\' => __(\''.$name.'\'))), \''.$this->getModuleName().'/'.$action.$url_params.($options ? ', '.$phpOptions : '').') ?]</li>'."\n";
  }

  /**
   * Returns HTML code for an action option in a select tag.
   *
   * @param string $actionName The action name
   * @param array  $params     The parameters
   *
   * @return string HTML code
   */
  public function getOptionToAction($actionName, $params)
  {
    $options = isset($params['params']) ? sfToolkit::stringToArray($params['params']) : array();

    // default values
    if ($actionName[0] == '_')
    {
      $actionName = substr($actionName, 1);
      if ($actionName == 'deleteSelected')
      {
        $params['name'] = 'Delete Selected';
      }
    }
    $name = isset($params['name']) ? $params['name'] : $actionName;

    $options['value'] = $actionName;

    $phpOptions = var_export($options, true);

    return '[?php echo content_tag(\'option\', __(\''.$name.'\')'.($options ? ', '.$phpOptions : '').') ?]';
  }

  /**
   * Returns HTML code for a column in edit mode.
   *
   * @param string $column The column name
   * @param array  $params The parameters
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
    return parent::getCrudColumnEditTag($column, $params);
  }

  /**
   * Returns all column categories.
   *
   * @param string $paramName The parameter name
   *
   * @return array The column categories
   */
  public function getColumnCategories($paramName)
  {
    if (is_array($this->getParameterValue($paramName)))
    {
      $fields = $this->getParameterValue($paramName);

      // do we have categories?
      if (!isset($fields[0]))
      {
        return array_keys($fields);
      }

    }

    return array('NONE');
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
      $credentials = str_replace("\n", ' ', var_export($params['credentials'], true));

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
   * Gets sfAdminColumn objects for a given category.
   *
   * @param string $paramName The parameter name
   * @param string $category  The category
   *
   * @return array sfAdminColumn array
   */
  public function getColumns($paramName, $category = 'NONE')
  {
    $phpNames = array();

    // user has set a personnalized list of fields?
    $fields = $this->getParameterValue($paramName);
    if (is_array($fields))
    {
      // categories?
      if (isset($fields[0]))
      {
        // simulate a default one
        $fields = array('NONE' => $fields);
      }

      if (!$fields)
      {
        return array();
      }

      foreach ($fields[$category] as $field)
      {
        list($field, $flags) = $this->splitFlag($field);

        $phpNames[] = $this->getAdminColumnForField($field, $flags);
      }
    }
    else
    {
      // no, just return the full list of columns in table
      return $this->getAllColumns();
    }

    return $phpNames;
  }

  /**
   * Gets modifier flags from a column name.
   *
   * @param string $text The column name
   *
   * @return array An array of detected flags
   */
  public function splitFlag($text)
  {
    $flags = array();
    while (in_array($text[0], array('=', '-', '+', '_', '~')))
    {
      $flags[] = $text[0];
      $text = substr($text, 1);
    }

    return array($text, $flags);
  }

  /**
   * Gets a parameter value.
   *
   * @param string $key     The key name
   * @param mixed  $default The default value
   *
   * @return mixed The parameter value
   */
  public function getParameterValue($key, $default = null)
  {
    if (preg_match('/^([^\.]+)\.fields\.(.+)$/', $key, $matches))
    {
      return $this->getFieldParameterValue($matches[2], $matches[1], $default);
    }
    else
    {
      return $this->getValueFromKey($key, $default);
    }
  }

  /**
   * Gets a field parameter value.
   *
   * @param string $key     The key name
   * @param string $type    The type (list, edit)
   * @param mixed  $default The default value
   *
   * @return mixed The parameter value
   */
  protected function getFieldParameterValue($key, $type = '', $default = null)
  {
    $retval = $this->getValueFromKey($type.'.fields.'.$key, $default);
    if ($retval !== null)
    {
      return $retval;
    }

    $retval = $this->getValueFromKey('fields.'.$key, $default);
    if ($retval !== null)
    {
      return $retval;
    }

    if (preg_match('/\.name$/', $key))
    {
      // default field.name
      return sfInflector::humanize(($pos = strpos($key, '.')) ? substr($key, 0, $pos) : $key);
    }
    else
    {
      return null;
    }
  }

  /**
   * Gets the value for a given key.
   *
   * @param string $key     The key name
   * @param mixed  $default The default value
   *
   * @return mixed The key value
   */
  protected function getValueFromKey($key, $default = null)
  {
    $ref   =& $this->params;
    $parts =  explode('.', $key);
    $count =  count($parts);
    for ($i = 0; $i < $count; $i++)
    {
      $partKey = $parts[$i];
      if (!isset($ref[$partKey]))
      {
        return $default;
      }

      if ($count == $i + 1)
      {
        return $ref[$partKey];
      }
      else
      {
        $ref =& $ref[$partKey];
      }
    }

    return $default;
  }

  /**
   * Wraps a content for I18N.
   *
   * @param string $key      The key name
   * @param string $default  The defaul value
   * @param bool   $withEcho If true, string is wrapped in php echo
   *
   * @return string HTML code
   */
  public function getI18NString($key, $default = null, $withEcho = true)
  {
    $value = $this->escapeString($this->getParameterValue($key, $default));

    // find %%xx%% strings
    preg_match_all('/%%([^%]+)%%/', $value, $matches, PREG_PATTERN_ORDER);
    $this->params['tmp']['display'] = array();
    foreach ($matches[1] as $name)
    {
      $this->params['tmp']['display'][] = $name;
    }

    $vars = array();
    foreach ($this->getColumns('tmp.display') as $column)
    {
      if ($column->isLink())
      {
        $vars[] = '\'%%'.$column->getName().'%%\' => link_to('.$this->getColumnListTag($column).', \''.$this->getModuleName().'/edit?'.$this->getPrimaryKeyUrlParams().')';
      }
      elseif ($column->isPartial())
      {
        $vars[] = '\'%%_'.$column->getName().'%%\' => '.$this->getColumnListTag($column);
      }
      else if ($column->isComponent())
      {
        $vars[] = '\'%%~'.$column->getName().'%%\' => '.$this->getColumnListTag($column);
      }
      else
      {
        $vars[] = '\'%%'.$column->getName().'%%\' => '.$this->getColumnListTag($column);
      }
    }

    // strip all = signs
    $value = preg_replace('/%%=([^%]+)%%/', '%%$1%%', $value);

    $i18n = '__(\''.$value.'\', '."\n".'array('.implode(",\n", $vars).'))';

    return $withEcho ? '[?php echo '.$i18n.' ?]' : $i18n;
  }

  /**
   * Replaces constants in a string.
   *
   * @param string $value
   *
   * @return string
   */
  public function replaceConstants($value)
  {
    // find %%xx%% strings
    preg_match_all('/%%([^%]+)%%/', $value, $matches, PREG_PATTERN_ORDER);
    $this->params['tmp']['display'] = array();
    foreach ($matches[1] as $name)
    {
      $this->params['tmp']['display'][] = $name;
    }

    foreach ($this->getColumns('tmp.display') as $column)
    {
      $value = str_replace('%%'.$column->getName().'%%', '{'.$this->getColumnGetter($column, true, 'this->').'}', $value);
    }

    return $value;
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
   * Returns HTML code for a column in filter mode.
   *
   * @param string $column The column name
   * @param array  $params The parameters
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
      return "get_component('".$this->getModuleName()."', '".$column->getName()."', array('type' => 'filter'))";
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
    else if ($type == PropelColumnTypes::CHAR || $type == PropelColumnTypes::VARCHAR || $type == PropelColumnTypes::LONGVARCHAR)
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
   * Gets the form object
   *
   * @return sfForm
   */
  public function getFormObject()
  {
    if (is_null($this->formObject))
    {
      $class = $this->getFormClassName();

      $this->formObject = new $class();
    }

    return $this->formObject;
  }

  /**
   * Gets the form class name
   *
   * @return string The form class name associated with this generator
   */
  public function getFormClassName()
  {
    return isset($this->params['form_class']) ? $this->params['form_class'] : $this->getClassName().'Form';
  }

  /**
   * Retrieves all hidden fields in the widget schema
   *
   * @return array
   */
  public function getHiddenFields()
  {
    $form = $this->getFormObject();
    $hiddenFields = array();
    foreach ($form->getWidgetSchema()->getPositions() as $name)
    {
      if ($form[$name]->isHidden())
      {
        $hiddenFields[] = $name;
      }
    }

    return $hiddenFields;
  }

  /**
   * Gets the hidden fields as a string
   *
   * @return array
   */
  public function getHiddenFieldsAsString()
  {
    $hiddenFields = '';
    foreach ($this->getHiddenFields() as $name)
    {
      $hiddenFields .= '        [?php echo $form[\''.$name.'\'] ?]'."\n";
    }

    return "\n".$hiddenFields;
  }

  public function getLastNonHiddenField()
  {
    $form = $this->getFormObject();
    $positions = $form->getWidgetSchema()->getPositions();
    $last = count($positions) - 1;
    for ($i = count($positions) - 1; $i >= 0; $i--)
    {
      if ($form[$positions[$i]]->isHidden())
      {
        $last = $i - 1;
      }
      else
      {
        break;
      }
    }

    return $last;
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
   * Escapes a string.
   *
   * @param string $string
   */
  protected function escapeString($string)
  {
    return preg_replace('/\'/', '\\\'', $string);
  }
}

/**
 * Admin generator column.
 *
 * @package    symfony
 * @subpackage generator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfAdminGenerator.class.php 17858 2009-05-01 21:22:50Z FabianLange $
 */
class sfAdminColumn
{
  protected
    $phpName    = '',
    $column     = null,
    $flags      = array();

  /**
   * Constructor.
   *
   * @param string $phpName The column php name
   * @param string $column  The column name
   * @param array  $flags   The column flags
   */
  public function __construct($phpName, $column = null, $flags = array())
  {
    $this->phpName = $phpName;
    $this->column  = $column;
    $this->flags   = (array) $flags;
  }

  /**
   * Returns true if the column maps a database column.
   *
   * @return boolean true if the column maps a database column, false otherwise
   */
  public function isReal()
  {
    return $this->column ? true : false;
  }

  /**
   * Gets the name of the column.
   *
   * @return string The column name
   */
  public function getName()
  {
    return sfInflector::underscore($this->phpName);
  }

  /**
   * Returns true if the column is a partial.
   *
   * @return boolean true if the column is a partial, false otherwise
   */
  public function isPartial()
  {
    return in_array('_', $this->flags) ? true : false;
  }

  /**
   * Returns true if the column is a component.
   *
   * @return boolean true if the column is a component, false otherwise
   */
  public function isComponent()
  {
    return in_array('~', $this->flags) ? true : false;
  }

  /**
   * Returns true if the column has a link.
   *
   * @return boolean true if the column has a link, false otherwise
   */
  public function isLink()
  {
    return (in_array('=', $this->flags) || $this->isPrimaryKey()) ? true : false;
  }

  /**
   * Gets the php name of the column.
   *
   * @return string The php name
   */
  public function getPhpName()
  {
    return $this->phpName;
  }

  // FIXME: those methods are only used in the propel admin generator
  public function __call($name, $arguments)
  {
    return $this->column ? $this->column->$name() : null;
  }
}
