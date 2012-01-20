<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) 2004-2006 Sean Kerr <sean@code-box.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * A view represents the presentation layer of an action. Output can be
 * customized by supplying attributes, which a template can manipulate and
 * display.
 *
 * @package    symfony
 * @subpackage view
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Sean Kerr <sean@code-box.org>
 * @version    SVN: $Id: sfView.class.php 28713 2010-03-23 15:08:22Z fabien $
 */
abstract class sfView
{
  /**
   * Show an alert view.
   */
  const ALERT = 'Alert';

  /**
   * Show an error view.
   */
  const ERROR = 'Error';

  /**
   * Show a form input view.
   */
  const INPUT = 'Input';

  /**
   * Skip view execution.
   */
  const NONE = 'None';

  /**
   * Show a success view.
   */
  const SUCCESS = 'Success';

  /**
   * Do not render the presentation.
   */
  const RENDER_NONE = 1;

  /**
   * Render the presentation to the client.
   */
  const RENDER_CLIENT = 2;

  /**
   * Render the presentation to a variable.
   */
  const RENDER_VAR = 4;

  /**
   * Skip view rendering but output http headers
   */
  const HEADER_ONLY = 8;

  protected
    $context            = null,
    $dispatcher         = null,
    $decorator          = false,
    $decoratorDirectory = null,
    $decoratorTemplate  = null,
    $directory          = null,
    $componentSlots     = array(),
    $template           = null,
    $attributeHolder    = null,
    $parameterHolder    = null,
    $moduleName         = '',
    $actionName         = '',
    $viewName           = '',
    $extension          = '.php';

  /**
   * Class constructor.
   *
   * @see initialize()
   */
  public function __construct($context, $moduleName, $actionName, $viewName)
  {
    $this->initialize($context, $moduleName, $actionName, $viewName);
  }

  /**
   * Initializes this view.
   *
   * @param  sfContext $context     The current application context
   * @param  string    $moduleName  The module name for this view
   * @param  string    $actionName  The action name for this view
   * @param  string    $viewName    The view name
   *
   * @return bool  true, if initialization completes successfully, otherwise false
   */
  public function initialize($context, $moduleName, $actionName, $viewName)
  {
    $this->moduleName = $moduleName;
    $this->actionName = $actionName;
    $this->viewName   = $viewName;

    $this->context    = $context;
    $this->dispatcher = $context->getEventDispatcher();

    sfOutputEscaper::markClassesAsSafe(array('sfForm', 'sfFormField', 'sfFormFieldSchema', 'sfModelGeneratorHelper'));

    $this->attributeHolder = $this->initializeAttributeHolder();

    $this->parameterHolder = new sfParameterHolder();
    $this->parameterHolder->add(sfConfig::get('mod_'.strtolower($moduleName).'_view_param', array()));

    $request = $context->getRequest();

    $format = $request->getRequestFormat();
    if (null !== $format)
    {
      if ('html' != $format)
      {
        $this->setExtension('.'.$format.$this->getExtension());
      }

      if ($mimeType = $request->getMimeType($format))
      {
        $this->context->getResponse()->setContentType($mimeType);

        if ('html' != $format)
        {
          $this->setDecorator(false);
        }
      }
    }
    $this->dispatcher->notify(new sfEvent($this, 'view.configure_format', array('format' => $format, 'response' => $context->getResponse(), 'request' => $context->getRequest())));

    // include view configuration
    $this->configure();

    return true;
  }

  protected function initializeAttributeHolder($attributes = array())
  {
    $attributeHolder = new sfViewParameterHolder($this->dispatcher, $attributes, array(
      'escaping_method'   => sfConfig::get('sf_escaping_method'),
      'escaping_strategy' => sfConfig::get('sf_escaping_strategy'),
    ));

    return $attributeHolder;
  }

  /**
   * Executes any presentation logic and set template attributes.
   */
  abstract function execute();

  /**
   * Configures template.
   */
  abstract function configure();

  /**
   * Retrieves this views decorator template directory.
   *
   * @return string An absolute filesystem path to this views decorator template directory
   */
  public function getDecoratorDirectory()
  {
    return $this->decoratorDirectory;
  }

  /**
   * Retrieves this views decorator template.
   *
   * @return string A template filename, if a template has been set, otherwise null
   */
  public function getDecoratorTemplate()
  {
    return $this->decoratorTemplate;
  }

  /**
   * Retrieves this view template directory.
   *
   * @return string An absolute filesystem path to this views template directory
   */
  public function getDirectory()
  {
    return $this->directory;
  }

  /**
   * Retrieves the template engine associated with this view.
   *
   * Note: This will return null for PHPView instances.
   *
   * @return mixed A template engine instance
   */
  abstract function getEngine();

  /**
   * Retrieves this views template.
   *
   * @return string A template filename, if a template has been set, otherwise null
   */
  public function getTemplate()
  {
    return $this->template;
  }

  /**
   * Retrieves attributes for the current view.
   *
   * @return sfParameterHolder The attribute parameter holder
   */
  public function getAttributeHolder()
  {
    return $this->attributeHolder;
  }

  /**
   * Retrieves an attribute for the current view.
   *
   * @param  string $name     Name of the attribute
   * @param  string $default  Value of the attribute
   *
   * @return mixed Attribute
   */
  public function getAttribute($name, $default = null)
  {
    return $this->attributeHolder->get($name, $default);
  }

  /**
   * Returns true if the view have attributes.
   *
   * @param  string $name  Name of the attribute
   *
   * @return mixed Attribute of the view
   */
  public function hasAttribute($name)
  {
    return $this->attributeHolder->has($name);
  }

  /**
   * Sets an attribute of the view.
   *
   * @param string $name   Attribute name
   * @param string $value  Value for the attribute
   */
  public function setAttribute($name, $value)
  {
    $this->attributeHolder->set($name, $value);
  }

  /**
   * Retrieves the parameters for the current view.
   *
   * @return sfParameterHolder The parameter holder
   */
  public function getParameterHolder()
  {
    return $this->parameterHolder;
  }

  /**
   * Retrieves a parameter from the current view.
   *
   * @param  string $name     Parameter name
   * @param  string $default  Default parameter value
   *
   * @return mixed A parameter value
   */
  public function getParameter($name, $default = null)
  {
    return $this->parameterHolder->get($name, $default);
  }

  /**
   * Indicates whether or not a parameter exist for the current view.
   *
   * @param  string $name  Name of the parameter
   *
   * @return bool true, if the parameter exists otherwise false
   */
  public function hasParameter($name)
  {
    return $this->parameterHolder->has($name);
  }

  /**
   * Sets a parameter for the view.
   *
   * @param string $name   Name of the parameter
   * @param string $value  The parameter value
   */
  public function setParameter($name, $value)
  {
    $this->parameterHolder->set($name, $value);
  }

  /**
   * Indicates that this view is a decorating view.
   *
   * @return bool true, if this view is a decorating view, otherwise false
   */
  public function isDecorator()
  {
    return $this->decorator;
  }

  /**
   * Sets the decorating mode for the current view.
   *
   * @param bool $boolean  Set the decorating mode for the view
   */
  public function setDecorator($boolean)
  {
    $this->decorator = (boolean) $boolean;

    if (false === $boolean)
    {
      $this->decoratorTemplate = false;
    }
  }

  /**
   * Executes a basic pre-render check to verify all required variables exist
   * and that the template is readable.
   *
   * @throws sfRenderException If the pre-render check fails
   */
  protected function preRenderCheck()
  {
    if (null === $this->template)
    {
      // a template has not been set
      throw new sfRenderException('A template has not been set.');
    }

    if (!is_readable($this->directory.'/'.$this->template))
    {
      // 404?
      if ('404' == $this->context->getResponse()->getStatusCode())
      {
        // use default exception templates
        $this->template = sfException::getTemplatePathForError($this->context->getRequest()->getRequestFormat(), false);
        $this->directory = dirname($this->template);
        $this->template = basename($this->template);
        $this->setAttribute('code', '404');
        $this->setAttribute('text', 'Not Found');
      }
      else
      {
        throw new sfRenderException(sprintf('The template "%s" does not exist or is unreadable in "%s".', $this->template, $this->directory));
      }
    }
  }

  /**
   * Renders the presentation.
   *
   * @return string A string representing the rendered presentation
   */
  abstract function render();

  /**
   * Sets the decorator template directory for this view.
   *
   * @param string $directory  An absolute filesystem path to a template directory
   */
  public function setDecoratorDirectory($directory)
  {
    $this->decoratorDirectory = $directory;
  }

  /**
   * Sets the decorator template for this view.
   *
   * If the template path is relative, it will be based on the currently
   * executing module's template sub-directory.
   *
   * @param string $template  An absolute or relative filesystem path to a template
   */
  public function setDecoratorTemplate($template)
  {
    if (false === $template)
    {
      $this->setDecorator(false);

      return;
    }
    else if (null === $template)
    {
      return;
    }

    if (!strpos($template, '.'))
    {
      $template .= $this->getExtension();
    }

    if (sfToolkit::isPathAbsolute($template))
    {
      $this->decoratorDirectory = dirname($template);
      $this->decoratorTemplate  = basename($template);
    }
    else
    {
      $this->decoratorDirectory = $this->context->getConfiguration()->getDecoratorDir($template);
      $this->decoratorTemplate = $template;
    }

    // set decorator status
    $this->decorator = true;
  }

  /**
   * Sets the template directory for this view.
   *
   * @param string $directory  An absolute filesystem path to a template directory
   */
  public function setDirectory($directory)
  {
    $this->directory = $directory;
  }

  /**
   * Sets the module and action to be executed in place of a particular template attribute.
   *
   * @param string $attributeName  A template attribute name
   * @param string $moduleName     A module name
   * @param string $componentName  A component name
   */
  public function setComponentSlot($attributeName, $moduleName, $componentName)
  {
    $this->componentSlots[$attributeName]                   = array();
    $this->componentSlots[$attributeName]['module_name']    = $moduleName;
    $this->componentSlots[$attributeName]['component_name'] = $componentName;
  }

  /**
   * Indicates whether or not a component slot exists.
   *
   * @param  string $name  The component slot name
   *
   * @return bool true, if the component slot exists, otherwise false
   */
  public function hasComponentSlot($name)
  {
    return isset($this->componentSlots[$name]);
  }

  /**
   * Gets a component slot
   *
   * @param  string $name  The component slot name
   *
   * @return array The component slot
   */
  public function getComponentSlot($name)
  {
    if (isset($this->componentSlots[$name]) && $this->componentSlots[$name]['module_name'] && $this->componentSlots[$name]['component_name'])
    {
      return array($this->componentSlots[$name]['module_name'], $this->componentSlots[$name]['component_name']);
    }

    return null;
  }

  /**
   * Sets the template for this view.
   *
   * If the template path is relative, it will be based on the currently
   * executing module's template sub-directory.
   *
   * @param string $template  An absolute or relative filesystem path to a template
   */
  public function setTemplate($template)
  {
    if (sfToolkit::isPathAbsolute($template))
    {
      $this->directory = dirname($template);
      $this->template  = basename($template);
    }
    else
    {
      $this->directory = $this->context->getConfiguration()->getTemplateDir($this->moduleName, $template);
      $this->template = $template;
    }
  }

  /**
   * Retrieves the current view extension.
   *
   * @return string The extension for current view.
   */
  public function getExtension()
  {
    return $this->extension;
  }

  /**
   * Sets an extension for the current view.
   *
   * @param string $extension  The extension name.
   */
  public function setExtension($extension)
  {
    $this->extension = $extension;
  }

  /**
   * Gets the module name associated with this view.
   *
   * @return string A module name
   */
  public function getModuleName()
  {
    return $this->moduleName;
  }

  /**
   * Gets the action name associated with this view.
   *
   * @return string An action name
   */
  public function getActionName()
  {
    return $this->actionName;
  }

  /**
   * Gets the view name associated with this view.
   *
   * @return string An action name
   */
  public function getViewName()
  {
    return $this->viewName;
  }

  /**
   * Calls methods defined via sfEventDispatcher.
   *
   * @param  string $method     The method name
   * @param  array  $arguments  The method arguments
   *
   * @return mixed The returned value of the called method
   *
   * @throws sfException< If the calls fails
   */
  public function __call($method, $arguments)
  {
    $event = $this->dispatcher->notifyUntil(new sfEvent($this, 'view.method_not_found', array('method' => $method, 'arguments' => $arguments)));
    if (!$event->isProcessed())
    {
      throw new sfException(sprintf('Call to undefined method %s::%s.', get_class($this), $method));
    }

    return $event->getReturnValue();
  }
}
