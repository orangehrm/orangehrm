<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfComponent.
 *
 * @package    symfony
 * @subpackage action
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfComponent.class.php 23922 2009-11-14 14:58:38Z fabien $
 */
abstract class sfComponent
{
  protected
    $moduleName             = '',
    $actionName             = '',
    $context                = null,
    $dispatcher             = null,
    $request                = null,
    $response               = null,
    $varHolder              = null,
    $requestParameterHolder = null;

  /**
   * Class constructor.
   *
   * @see initialize()
   */
  public function __construct($context, $moduleName, $actionName)
  {
    $this->initialize($context, $moduleName, $actionName);
  }

  /**
   * Initializes this component.
   *
   * @param sfContext $context    The current application context.
   * @param string    $moduleName The module name.
   * @param string    $actionName The action name.
   *
   * @return boolean true, if initialization completes successfully, otherwise false
   */
  public function initialize($context, $moduleName, $actionName)
  {
    $this->moduleName             = $moduleName;
    $this->actionName             = $actionName;
    $this->context                = $context;
    $this->dispatcher             = $context->getEventDispatcher();
    $this->varHolder              = new sfParameterHolder();
    $this->request                = $context->getRequest();
    $this->response               = $context->getResponse();
    $this->requestParameterHolder = $this->request->getParameterHolder();
  }

  /**
   * Execute any application/business logic for this component.
   *
   * In a typical database-driven application, execute() handles application
   * logic itself and then proceeds to create a model instance. Once the model
   * instance is initialized it handles all business logic for the action.
   *
   * A model should represent an entity in your application. This could be a
   * user account, a shopping cart, or even a something as simple as a
   * single product.
   *
   * @param sfRequest $request The current sfRequest object
   *
   * @return mixed     A string containing the view name associated with this action
   */
  abstract function execute($request);

  /**
   * Gets the module name associated with this component.
   *
   * @return string A module name
   */
  public function getModuleName()
  {
    return $this->moduleName;
  }

  /**
   * Gets the action name associated with this component.
   *
   * @return string An action name
   */
  public function getActionName()
  {
    return $this->actionName;
  }

  /**
   * Retrieves the current application context.
   *
   * @return sfContext The current sfContext instance
   */
  public final function getContext()
  {
    return $this->context;
  }

  /**
   * Retrieves the current logger instance.
   *
   * @return sfLogger The current sfLogger instance
   */
  public final function getLogger()
  {
    return $this->context->getLogger();
  }

  /**
   * Logs a message using the sfLogger object.
   *
   * @param mixed  $message  String or object containing the message to log
   * @param string $priority The priority of the message
   *                         (available priorities: emerg, alert, crit, err,
   *                         warning, notice, info, debug)
   *
   * @see sfLogger
   */
  public function logMessage($message, $priority = 'info')
  {
    if (sfConfig::get('sf_logging_enabled'))
    {
      $this->dispatcher->notify(new sfEvent($this, 'application.log', array($message, 'priority' => constant('sfLogger::'.strtoupper($priority)))));
    }
  }

  /**
   * Returns the value of a request parameter.
   *
   * This is a proxy method equivalent to:
   *
   * <code>$this->getRequest()->getParameterHolder()->get($name)</code>
   *
   * @param string $name    The parameter name
   * @param mixed  $default The default value if parameter does not exist
   *
   * @return string The request parameter value
   */
  public function getRequestParameter($name, $default = null)
  {
    return $this->requestParameterHolder->get($name, $default);
  }

  /**
   * Returns true if a request parameter exists.
   *
   * This is a proxy method equivalent to:
   *
   * <code>$this->getRequest()->getParameterHolder()->has($name)</code>
   *
   * @param string $name The parameter name
   * @return boolean true if the request parameter exists, false otherwise
   */
  public function hasRequestParameter($name)
  {
    return $this->requestParameterHolder->has($name);
  }

  /**
   * Retrieves the current sfRequest object.
   *
   * This is a proxy method equivalent to:
   *
   * <code>$this->getContext()->getRequest()</code>
   *
   * @return sfRequest The current sfRequest implementation instance
   */
  public function getRequest()
  {
    return $this->request;
  }

  /**
   * Retrieves the current sfResponse object.
   *
   * This is a proxy method equivalent to:
   *
   * <code>$this->getContext()->getResponse()</code>
   *
   * @return sfResponse The current sfResponse implementation instance
   */
  public function getResponse()
  {
    return $this->response;
  }

  /**
   * Retrieves the current sfController object.
   *
   * This is a proxy method equivalent to:
   *
   * <code>$this->getContext()->getController()</code>
   *
   * @return sfController The current sfController implementation instance
   */
  public function getController()
  {
    return $this->context->getController();
  }

  /**
   * Generates a URL for the given route and arguments.
   *
   * This is a proxy method equivalent to:
   *
   * <code>$this->getContext()->getRouting()->generate(...)</code>
   *
   * @param string  The route name
   * @param array   An array of parameters for the route
   * @param Boolean Whether to generate an absolute URL or not
   *
   * @return string  The URL
   */
  public function generateUrl($route, $params = array(), $absolute = false)
  {
    return $this->context->getRouting()->generate($route, $params, $absolute);
  }

  /**
   * Retrieves the current sfUser object.
   *
   * This is a proxy method equivalent to:
   *
   * <code>$this->getContext()->getUser()</code>
   *
   * @return sfUser The current sfUser implementation instance
   */
  public function getUser()
  {
    return $this->context->getUser();
  }

  /**
   * Gets the current mailer instance.
   *
   * @return sfMailer A sfMailer instance
   */
  public function getMailer()
  {
    return $this->getContext()->getMailer();
  }

  /**
   * Sets a variable for the template.
   *
   * If you add a safe value, the variable won't be output escaped
   * by symfony, so this is your responsability to ensure that the
   * value is escaped properly.
   *
   * @param string  $name  The variable name
   * @param mixed   $value The variable value
   * @param Boolean $safe  true if the value is safe for output (false by default)
   */
  public function setVar($name, $value, $safe = false)
  {
    $this->varHolder->set($name, $safe ? new sfOutputEscaperSafe($value) : $value);
  }

  /**
   * Gets a variable set for the template.
   *
   * @param string $name The variable name
   *
   * @return mixed  The variable value
   */
  public function getVar($name)
  {
    return $this->varHolder->get($name);
  }

  /**
   * Gets the sfParameterHolder object that stores the template variables.
   *
   * @return sfParameterHolder The variable holder.
   */
  public function getVarHolder()
  {
    return $this->varHolder;
  }

  /**
   * Sets a variable for the template.
   *
   * This is a shortcut for:
   *
   * <code>$this->setVar('name', 'value')</code>
   *
   * @param string $key   The variable name
   * @param string $value The variable value
   *
   * @return boolean always true
   *
   * @see setVar()
   */
  public function __set($key, $value)
  {
    return $this->varHolder->setByRef($key, $value);
  }

  /**
   * Gets a variable for the template.
   *
   * This is a shortcut for:
   *
   * <code>$this->getVar('name')</code>
   *
   * @param string $key The variable name
   *
   * @return mixed The variable value
   *
   * @see getVar()
   */
  public function & __get($key)
  {
    return $this->varHolder->get($key);
  }

  /**
   * Returns true if a variable for the template is set.
   *
   * This is a shortcut for:
   *
   * <code>$this->getVarHolder()->has('name')</code>
   *
   * @param string $name The variable name
   *
   * @return boolean true if the variable is set
   */
  public function __isset($name)
  {
    return $this->varHolder->has($name);
  }

  /**
   * Removes a variable for the template.
   *
   * This is just really a shortcut for:
   *
   * <code>$this->getVarHolder()->remove('name')</code>
   *
   * @param string $name The variable Name
   */
  public function __unset($name)
  {
    $this->varHolder->remove($name);
  }

  /**
   * Calls methods defined via sfEventDispatcher.
   *
   * @param string $method The method name
   * @param array  $arguments The method arguments
   *
   * @return mixed The returned value of the called method
   *
   * @throws sfException If called method is undefined
   */
  public function __call($method, $arguments)
  {
    $event = $this->dispatcher->notifyUntil(new sfEvent($this, 'component.method_not_found', array('method' => $method, 'arguments' => $arguments)));
    if (!$event->isProcessed())
    {
      throw new sfException(sprintf('Call to undefined method %s::%s.', get_class($this), $method));
    }

    return $event->getReturnValue();
  }
}
