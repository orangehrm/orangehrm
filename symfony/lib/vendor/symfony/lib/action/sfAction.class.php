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
 * sfAction executes all the logic for the current request.
 *
 * @package    symfony
 * @subpackage action
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Sean Kerr <sean@code-box.org>
 * @version    SVN: $Id: sfAction.class.php 24279 2009-11-23 15:21:18Z fabien $
 */
abstract class sfAction extends sfComponent
{
  protected
    $security = array();

  /**
   * Initializes this action.
   *
   * @param sfContext $context    The current application context.
   * @param string    $moduleName The module name.
   * @param string    $actionName The action name.
   *
   * @return bool true, if initialization completes successfully, otherwise false
   */
  public function initialize($context, $moduleName, $actionName)
  {
    parent::initialize($context, $moduleName, $actionName);

    // include security configuration
    if ($file = $context->getConfigCache()->checkConfig('modules/'.$this->getModuleName().'/config/security.yml', true))
    {
      require($file);
    }
  }

  /**
   * Executes an application defined process prior to execution of this sfAction object.
   *
   * By default, this method is empty.
   */
  public function preExecute()
  {
  }

  /**
   * Execute an application defined process immediately after execution of this sfAction object.
   *
   * By default, this method is empty.
   */
  public function postExecute()
  {
  }

  /**
   * Forwards current action to the default 404 error action.
   *
   * @param string $message Message of the generated exception
   *
   * @throws sfError404Exception
   *
   */
  public function forward404($message = null)
  {
    throw new sfError404Exception($this->get404Message($message));
  }

  /**
   * Forwards current action to the default 404 error action unless the specified condition is true.
   *
   * @param bool    $condition  A condition that evaluates to true or false
   * @param string  $message    Message of the generated exception
   *
   * @throws sfError404Exception
   */
  public function forward404Unless($condition, $message = null)
  {
    if (!$condition)
    {
      throw new sfError404Exception($this->get404Message($message));
    }
  }

  /**
   * Forwards current action to the default 404 error action if the specified condition is true.
   *
   * @param bool    $condition  A condition that evaluates to true or false
   * @param string  $message    Message of the generated exception
   *
   * @throws sfError404Exception
   */
  public function forward404If($condition, $message = null)
  {
    if ($condition)
    {
      throw new sfError404Exception($this->get404Message($message));
    }
  }

  /**
   * Redirects current action to the default 404 error action (with browser redirection).
   *
   * This method stops the current code flow.
   */
  public function redirect404()
  {
    return $this->redirect('/'.sfConfig::get('sf_error_404_module').'/'.sfConfig::get('sf_error_404_action'));
  }

  /**
   * Forwards current action to a new one (without browser redirection).
   *
   * This method stops the action. So, no code is executed after a call to this method.
   *
   * @param  string  $module  A module name
   * @param  string  $action  An action name
   *
   * @throws sfStopException
   */
  public function forward($module, $action)
  {
    if (sfConfig::get('sf_logging_enabled'))
    {
      $this->dispatcher->notify(new sfEvent($this, 'application.log', array(sprintf('Forward to action "%s/%s"', $module, $action))));
    }

    $this->getController()->forward($module, $action);

    throw new sfStopException();
  }

  /**
   * If the condition is true, forwards current action to a new one (without browser redirection).
   *
   * This method stops the action. So, no code is executed after a call to this method.
   *
   * @param  bool   $condition  A condition that evaluates to true or false
   * @param  string $module     A module name
   * @param  string $action     An action name
   *
   * @throws sfStopException
   */
  public function forwardIf($condition, $module, $action)
  {
    if ($condition)
    {
      $this->forward($module, $action);
    }
  }

  /**
   * Unless the condition is true, forwards current action to a new one (without browser redirection).
   *
   * This method stops the action. So, no code is executed after a call to this method.
   *
   * @param  bool   $condition  A condition that evaluates to true or false
   * @param  string $module     A module name
   * @param  string $action     An action name
   *
   * @throws sfStopException
   */
  public function forwardUnless($condition, $module, $action)
  {
    if (!$condition)
    {
      $this->forward($module, $action);
    }
  }

  /**
   * Redirects current request to a new URL.
   *
   * 2 URL formats are accepted :
   *  - a full URL: http://www.google.com/
   *  - an internal URL (url_for() format): module/action
   *
   * This method stops the action. So, no code is executed after a call to this method.
   *
   * @param  string $url         Url
   * @param  string $statusCode  Status code (default to 302)
   *
   * @throws sfStopException
   */
  public function redirect($url, $statusCode = 302)
  {
    // compatibility with url_for2() style signature
    if (is_object($statusCode) || is_array($statusCode))
    {
      $url = array_merge(array('sf_route' => $url), is_object($statusCode) ? array('sf_subject' => $statusCode) : $statusCode);
      $statusCode = func_num_args() >= 3 ? func_get_arg(2) : 302;
    }

    $this->getController()->redirect($url, 0, $statusCode);

    throw new sfStopException();
  }

  /**
   * Redirects current request to a new URL, only if specified condition is true.
   *
   * This method stops the action. So, no code is executed after a call to this method.
   *
   * @param  bool   $condition  A condition that evaluates to true or false
   * @param  string $url        Url
   * @param  string $statusCode Status code (default to 302)
   *
   * @throws sfStopException
   *
   * @see redirect
   */
  public function redirectIf($condition, $url, $statusCode = 302)
  {
    if ($condition)
    {
      // compatibility with url_for2() style signature
      $arguments = func_get_args();
      call_user_func_array(array($this, 'redirect'), array_slice($arguments, 1));
    }
  }

  /**
   * Redirects current request to a new URL, unless specified condition is true.
   *
   * This method stops the action. So, no code is executed after a call to this method.
   *
   * @param  bool   $condition  A condition that evaluates to true or false
   * @param  string $url        Url
   * @param  string $statusCode Status code (default to 302)
   *
   * @throws sfStopException
   *
   * @see redirect
   */
  public function redirectUnless($condition, $url, $statusCode = 302)
  {
    if (!$condition)
    {
      // compatibility with url_for2() style signature
      $arguments = func_get_args();
      call_user_func_array(array($this, 'redirect'), array_slice($arguments, 1));
    }
  }

  /**
   * Appends the given text to the response content and bypasses the built-in view system.
   *
   * This method must be called as with a return:
   *
   * <code>return $this->renderText('some text')</code>
   *
   * @param string $text Text to append to the response
   *
   * @return sfView::NONE
   */
  public function renderText($text)
  {
    $this->getResponse()->setContent($this->getResponse()->getContent().$text);

    return sfView::NONE;
  }

  /**
   * Returns the partial rendered content.
   *
   * If the vars parameter is omitted, the action's internal variables
   * will be passed, just as it would to a normal template.
   *
   * If the vars parameter is set then only those values are
   * available in the partial.
   *
   * @param  string $templateName partial name
   * @param  array  $vars         vars
   *
   * @return string The partial content
   */
  public function getPartial($templateName, $vars = null)
  {
    $this->getContext()->getConfiguration()->loadHelpers('Partial');

    $vars = null !== $vars ? $vars : $this->varHolder->getAll();

    return get_partial($templateName, $vars);
  }

  /**
   * Appends the result of the given partial execution to the response content.
   *
   * This method must be called as with a return:
   *
   * <code>return $this->renderPartial('foo/bar')</code>
   *
   * @param  string $templateName partial name
   * @param  array  $vars         vars
   *
   * @return sfView::NONE
   *
   * @see    getPartial
   */
  public function renderPartial($templateName, $vars = null)
  {
    return $this->renderText($this->getPartial($templateName, $vars));
  }

  /**
   * Returns the component rendered content.
   *
   * If the vars parameter is omitted, the action's internal variables
   * will be passed, just as it would to a normal template.
   *
   * If the vars parameter is set then only those values are
   * available in the component.
   *
   * @param  string  $moduleName    module name
   * @param  string  $componentName  component name
   * @param  array   $vars          vars
   *
   * @return string  The component rendered content
   */
  public function getComponent($moduleName, $componentName, $vars = null)
  {
    $this->getContext()->getConfiguration()->loadHelpers('Partial');

    $vars = null !== $vars ? $vars : $this->varHolder->getAll();

    return get_component($moduleName, $componentName, $vars);
  }

  /**
   * Appends the result of the given component execution to the response content.
   *
   * This method must be called as with a return:
   *
   * <code>return $this->renderComponent('foo', 'bar')</code>
   *
   * @param  string  $moduleName    module name
   * @param  string  $componentName  component name
   * @param  array   $vars          vars
   *
   * @return sfView::NONE
   *
   * @see    getComponent
   */
  public function renderComponent($moduleName, $componentName, $vars = null)
  {
    return $this->renderText($this->getComponent($moduleName, $componentName, $vars));
  }

  /**
   * Returns the security configuration for this module.
   *
   * @return string Current security configuration as an array
   */
  public function getSecurityConfiguration()
  {
    return $this->security;
  }

  /**
   * Overrides the current security configuration for this module.
   *
   * @param array $security The new security configuration
   */
  public function setSecurityConfiguration($security)
  {
    $this->security = $security;
  }

  /**
   * Returns a value from security.yml.
   *
   * @param string $name    The name of the value to pull from security.yml
   * @param mixed  $default The default value to return if none is found in security.yml
   *
   * @return mixed
   */
  public function getSecurityValue($name, $default = null)
  {
    $actionName = strtolower($this->getActionName());

    if (isset($this->security[$actionName][$name]))
    {
      return $this->security[$actionName][$name];
    }

    if (isset($this->security['all'][$name]))
    {
      return $this->security['all'][$name];
    }

    return $default;
  }

  /**
   * Indicates that this action requires security.
   *
   * @return bool true, if this action requires security, otherwise false.
   */
  public function isSecure()
  {
    return $this->getSecurityValue('is_secure', false);
  }

  /**
   * Gets credentials the user must have to access this action.
   *
   * @return mixed An array or a string describing the credentials the user must have to access this action
   */
  public function getCredential()
  {
    return $this->getSecurityValue('credentials');
  }

  /**
   * Sets an alternate template for this sfAction.
   *
   * See 'Naming Conventions' in the 'Symfony View' documentation.
   *
   * @param string $name    Template name
   * @param string $module  The module (current if null)
   */
  public function setTemplate($name, $module = null)
  {
    if (sfConfig::get('sf_logging_enabled'))
    {
      $this->dispatcher->notify(new sfEvent($this, 'application.log', array(sprintf('Change template to "%s/%s"', null === $module ? 'CURRENT' : $module, $name))));
    }

    if (null !== $module)
    {
      $name = sfConfig::get('sf_app_dir').'/modules/'.$module.'/templates/'.$name;
    }

    sfConfig::set('symfony.view.'.$this->getModuleName().'_'.$this->getActionName().'_template', $name);
  }

  /**
   * Gets the name of the alternate template for this sfAction.
   *
   * WARNING: It only returns the template you set with the setTemplate() method,
   *          and does not return the template that you configured in your view.yml.
   *
   * See 'Naming Conventions' in the 'Symfony View' documentation.
   *
   * @return string Template name. Returns null if no template has been set within the action
   */
  public function getTemplate()
  {
    return sfConfig::get('symfony.view.'.$this->getModuleName().'_'.$this->getActionName().'_template');
  }

  /**
   * Sets an alternate layout for this sfAction.
   *
   * To de-activate the layout, set the layout name to false.
   *
   * To revert the layout to the one configured in the view.yml, set the template name to null.
   *
   * @param mixed $name Layout name or false to de-activate the layout
   */
  public function setLayout($name)
  {
    if (sfConfig::get('sf_logging_enabled'))
    {
      $this->dispatcher->notify(new sfEvent($this, 'application.log', array(sprintf('Change layout to "%s"', $name))));
    }

    sfConfig::set('symfony.view.'.$this->getModuleName().'_'.$this->getActionName().'_layout', $name);
  }

  /**
   * Gets the name of the alternate layout for this sfAction.
   *
   * WARNING: It only returns the layout you set with the setLayout() method,
   *          and does not return the layout that you configured in your view.yml.
   *
   * @return mixed Layout name. Returns null if no layout has been set within the action
   */
  public function getLayout()
  {
    return sfConfig::get('symfony.view.'.$this->getModuleName().'_'.$this->getActionName().'_layout');
  }

  /**
   * Changes the default view class used for rendering the template associated with the current action.
   *
   * @param string $class View class name
   */
  public function setViewClass($class)
  {
    sfConfig::set('mod_'.strtolower($this->getModuleName()).'_view_class', $class);
  }

  /**
   * Returns the current route for this request
   *
   * @return sfRoute The route for the request
   */
  public function getRoute()
  {
    return $this->getRequest()->getAttribute('sf_route');
  }

  /**
   * Returns a formatted message for a 404 error.
   *
   * @param  string $message An error message (null by default)
   *
   * @return string The error message or a default one if null
   */
  protected function get404Message($message = null)
  {
    return null === $message ? sprintf('This request has been forwarded to a 404 error page by the action "%s/%s".', $this->getModuleName(), $this->getActionName()) : $message;
  }
}
