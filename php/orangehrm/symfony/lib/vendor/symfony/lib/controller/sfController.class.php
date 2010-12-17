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
 * sfController directs application flow.
 *
 * @package    symfony
 * @subpackage controller
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Sean Kerr <sean@code-box.org>
 * @version    SVN: $Id: sfController.class.php 30912 2010-09-15 11:10:46Z fabien $
 */
abstract class sfController
{
  protected
    $context           = null,
    $dispatcher        = null,
    $controllerClasses = array(),
    $renderMode        = sfView::RENDER_CLIENT,
    $maxForwards       = 5;

  /**
   * Class constructor.
   *
   * @see initialize()
   */
  public function __construct($context)
  {
    $this->initialize($context);
  }

  /**
   * Initializes this controller.
   *
   * @param sfContext $context A sfContext implementation instance
   */
  public function initialize($context)
  {
    $this->context    = $context;
    $this->dispatcher = $context->getEventDispatcher();
  }

  /**
   * Indicates whether or not a module has a specific component.
   *
   * @param string $moduleName    A module name
   * @param string $componentName An component name
   *
   * @return bool true, if the component exists, otherwise false
   */
  public function componentExists($moduleName, $componentName)
  {
    return $this->controllerExists($moduleName, $componentName, 'component', false);
  }

  /**
   * Indicates whether or not a module has a specific action.
   *
   * @param string $moduleName A module name
   * @param string $actionName An action name
   *
   * @return bool true, if the action exists, otherwise false
   */
  public function actionExists($moduleName, $actionName)
  {
    return $this->controllerExists($moduleName, $actionName, 'action', false);
  }

  /**
   * Looks for a controller and optionally throw exceptions if existence is required (i.e.
   * in the case of {@link getController()}).
   *
   * @param string  $moduleName      The name of the module
   * @param string  $controllerName  The name of the controller within the module
   * @param string  $extension       Either 'action' or 'component' depending on the type of controller to look for
   * @param boolean $throwExceptions Whether to throw exceptions if the controller doesn't exist
   *
   * @throws sfConfigurationException thrown if the module is not enabled
   * @throws sfControllerException    thrown if the controller doesn't exist and the $throwExceptions parameter is set to true
   *
   * @return boolean true if the controller exists, false otherwise
   */
  protected function controllerExists($moduleName, $controllerName, $extension, $throwExceptions)
  {
    $dirs = $this->context->getConfiguration()->getControllerDirs($moduleName);
    foreach ($dirs as $dir => $checkEnabled)
    {
      // plugin module enabled?
      if ($checkEnabled && !in_array($moduleName, sfConfig::get('sf_enabled_modules')) && is_readable($dir))
      {
        throw new sfConfigurationException(sprintf('The module "%s" is not enabled.', $moduleName));
      }

      // check for a module generator config file
      $this->context->getConfigCache()->import('modules/'.$moduleName.'/config/generator.yml', false, true);

      // one action per file or one file for all actions
      $classFile   = strtolower($extension);
      $classSuffix = ucfirst(strtolower($extension));
      $file        = $dir.'/'.$controllerName.$classSuffix.'.class.php';
      if (is_readable($file))
      {
        // action class exists
        require_once($file);

        $this->controllerClasses[$moduleName.'_'.$controllerName.'_'.$classSuffix] = $controllerName.$classSuffix;

        return true;
      }

      $module_file = $dir.'/'.$classFile.'s.class.php';
      if (is_readable($module_file))
      {
        // module class exists
        require_once($module_file);

        if (!class_exists($moduleName.$classSuffix.'s', false))
        {
          if ($throwExceptions)
          {
            throw new sfControllerException(sprintf('There is no "%s" class in your action file "%s".', $moduleName.$classSuffix.'s', $module_file));
          }

          return false;
        }

        // action is defined in this class?
        if (!in_array('execute'.ucfirst($controllerName), get_class_methods($moduleName.$classSuffix.'s')))
        {
          if ($throwExceptions)
          {
            throw new sfControllerException(sprintf('There is no "%s" method in your action class "%s".', 'execute'.ucfirst($controllerName), $moduleName.$classSuffix.'s'));
          }

          return false;
        }

        $this->controllerClasses[$moduleName.'_'.$controllerName.'_'.$classSuffix] = $moduleName.$classSuffix.'s';
        return true;
      }
    }

    // send an exception if debug
    if ($throwExceptions && sfConfig::get('sf_debug'))
    {
      $dirs = array_map(array('sfDebug', 'shortenFilePath'), array_keys($dirs));

      throw new sfControllerException(sprintf('Controller "%s/%s" does not exist in: %s.', $moduleName, $controllerName, implode(', ', $dirs)));
    }

    return false;
  }

  /**
   * Forwards the request to another action.
   *
   * @param string $moduleName A module name
   * @param string $actionName An action name
   *
   * @throws sfConfigurationException  If an invalid configuration setting has been found
   * @throws sfForwardException        If an error occurs while forwarding the request
   * @throws sfError404Exception       If the action not exist
   * @throws sfInitializationException If the action could not be initialized
   */
  public function forward($moduleName, $actionName)
  {
    // replace unwanted characters
    $moduleName = preg_replace('/[^a-z0-9_]+/i', '', $moduleName);
    $actionName = preg_replace('/[^a-z0-9_]+/i', '', $actionName);

    if ($this->getActionStack()->getSize() >= $this->maxForwards)
    {
      // let's kill this party before it turns into cpu cycle hell
      throw new sfForwardException('Too many forwards have been detected for this request.');
    }

    // check for a module generator config file
    $this->context->getConfigCache()->import('modules/'.$moduleName.'/config/generator.yml', false, true);

    if (!$this->actionExists($moduleName, $actionName))
    {
      // the requested action doesn't exist
      if (sfConfig::get('sf_logging_enabled'))
      {
        $this->dispatcher->notify(new sfEvent($this, 'application.log', array(sprintf('Action "%s/%s" does not exist', $moduleName, $actionName))));
      }

      throw new sfError404Exception(sprintf('Action "%s/%s" does not exist.', $moduleName, $actionName));
    }

    // create an instance of the action
    $actionInstance = $this->getAction($moduleName, $actionName);

    // add a new action stack entry
    $this->getActionStack()->addEntry($moduleName, $actionName, $actionInstance);

    // include module configuration
    $viewClass = sfConfig::get('mod_'.strtolower($moduleName).'_view_class', false);
    require($this->context->getConfigCache()->checkConfig('modules/'.$moduleName.'/config/module.yml'));
    if (false !== $viewClass)
    {
      sfConfig::set('mod_'.strtolower($moduleName).'_view_class', $viewClass);
    }

    // module enabled?
    if (sfConfig::get('mod_'.strtolower($moduleName).'_enabled'))
    {
      // check for a module config.php
      $moduleConfig = sfConfig::get('sf_app_module_dir').'/'.$moduleName.'/config/config.php';
      if (is_readable($moduleConfig))
      {
        require_once($moduleConfig);
      }

      // create a new filter chain
      $filterChain = new sfFilterChain();
      $filterChain->loadConfiguration($actionInstance);

      $this->context->getEventDispatcher()->notify(new sfEvent($this, 'controller.change_action', array('module' => $moduleName, 'action' => $actionName)));

      if ($moduleName == sfConfig::get('sf_error_404_module') && $actionName == sfConfig::get('sf_error_404_action'))
      {
        $this->context->getResponse()->setStatusCode(404);
        $this->context->getResponse()->setHttpHeader('Status', '404 Not Found');

        $this->dispatcher->notify(new sfEvent($this, 'controller.page_not_found', array('module' => $moduleName, 'action' => $actionName)));
      }

      // process the filter chain
      $filterChain->execute();
    }
    else
    {
      $moduleName = sfConfig::get('sf_module_disabled_module');
      $actionName = sfConfig::get('sf_module_disabled_action');

      if (!$this->actionExists($moduleName, $actionName))
      {
        // cannot find mod disabled module/action
        throw new sfConfigurationException(sprintf('Invalid configuration settings: [sf_module_disabled_module] "%s", [sf_module_disabled_action] "%s".', $moduleName, $actionName));
      }

      $this->forward($moduleName, $actionName);
    }
  }

  /**
   * Retrieves an sfAction implementation instance.
   *
   * @param string $moduleName A module name
   * @param string $actionName An action name
   *
   * @return sfAction An sfAction implementation instance, if the action exists, otherwise null
   */
  public function getAction($moduleName, $actionName)
  {
    return $this->getController($moduleName, $actionName, 'action');
  }

  /**
   * Retrieves a sfComponent implementation instance.
   *
   * @param string $moduleName    A module name
   * @param string $componentName A component name
   *
   * @return sfComponent A sfComponent implementation instance, if the component exists, otherwise null
   */
  public function getComponent($moduleName, $componentName)
  {
    return $this->getController($moduleName, $componentName, 'component');
  }

  /**
   * Retrieves a controller implementation instance.
   *
   * @param string $moduleName     A module name
   * @param string $controllerName A component name
   * @param string $extension      Either 'action' or 'component' depending on the type of controller to look for
   *
   * @return object A controller implementation instance, if the controller exists, otherwise null
   *
   * @see getComponent(), getAction()
   */
  protected function getController($moduleName, $controllerName, $extension)
  {
    $classSuffix = ucfirst(strtolower($extension));
    if (!isset($this->controllerClasses[$moduleName.'_'.$controllerName.'_'.$classSuffix]))
    {
      $this->controllerExists($moduleName, $controllerName, $extension, true);
    }

    $class = $this->controllerClasses[$moduleName.'_'.$controllerName.'_'.$classSuffix];

    // fix for same name classes
    $moduleClass = $moduleName.'_'.$class;

    if (class_exists($moduleClass, false))
    {
      $class = $moduleClass;
    }

    return new $class($this->context, $moduleName, $controllerName);
  }

  /**
   * Retrieves the action stack.
   *
   * @return sfActionStack An sfActionStack instance, if the action stack is enabled, otherwise null
   */
  public function getActionStack()
  {
    return $this->context->getActionStack();
  }

  /**
   * Retrieves the presentation rendering mode.
   *
   * @return int One of the following:
   *             - sfView::RENDER_CLIENT
   *             - sfView::RENDER_VAR
   */
  public function getRenderMode()
  {
    return $this->renderMode;
  }

  /**
   * Retrieves a sfView implementation instance.
   *
   * @param string $moduleName A module name
   * @param string $actionName An action name
   * @param string $viewName   A view name
   *
   * @return sfView A sfView implementation instance, if the view exists, otherwise null
   */
  public function getView($moduleName, $actionName, $viewName)
  {
    // user view exists?
    $file = sfConfig::get('sf_app_module_dir').'/'.$moduleName.'/view/'.$actionName.$viewName.'View.class.php';

    if (is_readable($file))
    {
      require_once($file);

      $class = $actionName.$viewName.'View';

      // fix for same name classes
      $moduleClass = $moduleName.'_'.$class;

      if (class_exists($moduleClass, false))
      {
        $class = $moduleClass;
      }
    }
    else
    {
      // view class (as configured in module.yml or defined in action)
      $class = sfConfig::get('mod_'.strtolower($moduleName).'_view_class', 'sfPHP').'View';
    }

    return new $class($this->context, $moduleName, $actionName, $viewName);
  }

  /**
   * Returns the rendered view presentation of a given module/action.
   *
   * @param string $module   A module name
   * @param string $action   An action name
   * @param string $viewName A View class name
   *
   * @return string The generated content
   */
  public function getPresentationFor($module, $action, $viewName = null)
  {
    if (sfConfig::get('sf_logging_enabled'))
    {
      $this->dispatcher->notify(new sfEvent($this, 'application.log', array(sprintf('Get presentation for action "%s/%s" (view class: "%s")', $module, $action, $viewName))));
    }

    // get original render mode
    $renderMode = $this->getRenderMode();

    // set render mode to var
    $this->setRenderMode(sfView::RENDER_VAR);

    // grab the action stack
    $actionStack = $this->getActionStack();

    // grab this next forward's action stack index
    $index = $actionStack->getSize();

    // set viewName if needed
    if ($viewName)
    {
      $currentViewName = sfConfig::get('mod_'.strtolower($module).'_view_class');
      sfConfig::set('mod_'.strtolower($module).'_view_class', $viewName);
    }

    try
    {
      // forward to the action
      $this->forward($module, $action);
    }
    catch (Exception $e)
    {
      // put render mode back
      $this->setRenderMode($renderMode);

      // remove viewName
      if ($viewName)
      {
        sfConfig::set('mod_'.strtolower($module).'_view_class', $currentViewName);
      }

      throw $e;
    }

    // grab the action entry from this forward
    $actionEntry = $actionStack->getEntry($index);

    // get raw content
    $presentation =& $actionEntry->getPresentation();

    // put render mode back
    $this->setRenderMode($renderMode);

    // remove the action entry
    $nb = $actionStack->getSize() - $index;
    while ($nb-- > 0)
    {
      $actionEntry = $actionStack->popEntry();

      if ($actionEntry->getModuleName() == sfConfig::get('sf_login_module') && $actionEntry->getActionName() == sfConfig::get('sf_login_action'))
      {
        throw new sfException('Your action is secured, but the user is not authenticated.');
      }
      else if ($actionEntry->getModuleName() == sfConfig::get('sf_secure_module') && $actionEntry->getActionName() == sfConfig::get('sf_secure_action'))
      {
        throw new sfException('Your action is secured, but the user does not have access.');
      }
    }

    // remove viewName
    if ($viewName)
    {
      sfConfig::set('mod_'.strtolower($module).'_view_class', $currentViewName);
    }

    return $presentation;
  }

  /**
   * Sets the presentation rendering mode.
   *
   * @param int $mode A rendering mode one of the following:
   *                  - sfView::RENDER_CLIENT
   *                  - sfView::RENDER_VAR
   *                  - sfView::RENDER_NONE
   *
   * @return true
   *
   * @throws sfRenderException If an invalid render mode has been set
   */
  public function setRenderMode($mode)
  {
    if ($mode == sfView::RENDER_CLIENT || $mode == sfView::RENDER_VAR || $mode == sfView::RENDER_NONE)
    {
      $this->renderMode = $mode;

      return;
    }

    // invalid rendering mode type
    throw new sfRenderException(sprintf('Invalid rendering mode: %s.', $mode));
  }

  /**
   * Indicates whether or not we were called using the CLI version of PHP.
   *
   * @return bool true, if using cli, otherwise false.
   */
  public function inCLI()
  {
    return 0 == strncasecmp(PHP_SAPI, 'cli', 3);
  }

  /**
   * Calls methods defined via sfEventDispatcher.
   *
   * @param string $method    The method name
   * @param array  $arguments The method arguments
   *
   * @return mixed The returned value of the called method
   */
  public function __call($method, $arguments)
  {
    $event = $this->dispatcher->notifyUntil(new sfEvent($this, 'controller.method_not_found', array('method' => $method, 'arguments' => $arguments)));
    if (!$event->isProcessed())
    {
      throw new sfException(sprintf('Call to undefined method %s::%s.', get_class($this), $method));
    }

    return $event->getReturnValue();
  }
}
