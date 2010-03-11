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
 * sfContext provides information about the current application context, such as
 * the module and action names and the module directory. References to the
 * main symfony instances are also provided.
 *
 * @package    symfony
 * @subpackage util
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Sean Kerr <sean@code-box.org>
 * @version    SVN: $Id: sfContext.class.php 16165 2009-03-11 07:16:24Z fabien $
 */
class sfContext
{
  protected
    $dispatcher    = null,
    $configuration = null,
    $factories     = array();

  protected static
    $instances = array(),
    $current   = 'default';

  /**
   * Creates a new context instance.
   *
   * @param  sfApplicationConfiguration $configuration  An sfApplicationConfiguration instance
   * @param  string                     $name           A name for this context (application name by default)
   * @param  string                     $class          The context class to use (sfContext by default)
   *
   * @return sfContext                  An sfContext instance
   */
  static public function createInstance(sfApplicationConfiguration $configuration, $name = null, $class = __CLASS__)
  {
    if (is_null($name))
    {
      $name = $configuration->getApplication();
    }

    self::$current = $name;

    self::$instances[$name] = new $class();

    if (!self::$instances[$name] instanceof sfContext)
    {
      throw new sfFactoryException(sprintf('Class "%s" is not of the type sfContext.', $class));
    }

    self::$instances[$name]->initialize($configuration);

    return self::$instances[$name];
  }

  /**
   * Initializes the current sfContext instance.
   *
   * @param sfApplicationConfiguration $configuration  An sfApplicationConfiguration instance
   */
  public function initialize(sfApplicationConfiguration $configuration)
  {
    $this->configuration = $configuration;
    $this->dispatcher    = $configuration->getEventDispatcher();

    try
    {
      $this->loadFactories();
    }
    catch (sfException $e)
    {
      $e->printStackTrace();
    }
    catch (Exception $e)
    {
      sfException::createFromException($e)->printStackTrace();
    }

    $this->dispatcher->connect('template.filter_parameters', array($this, 'filterTemplateParameters'));

    // register our shutdown function
    register_shutdown_function(array($this, 'shutdown'));
  }

  /**
   * Retrieves the singleton instance of this class.
   *
   * @param  string    $name   The name of the sfContext to retrieve.
   * @param  string    $class  The context class to use (sfContext by default)
   *
   * @return sfContext An sfContext implementation instance.
   */
  static public function getInstance($name = null, $class = __CLASS__)
  {
    if (is_null($name))
    {
      $name = self::$current;
    }

    if (!isset(self::$instances[$name]))
    {
      throw new sfException(sprintf('The "%s" context does not exist.', $name));
    }

    return self::$instances[$name];
  }

  /**
   * Checks to see if there has been a context created
   *
   * @param  string $name  The name of the sfContext to check for
   *
   * @return bool true is instanced, otherwise false
   */

  public static function hasInstance($name = null)
  {
    if (is_null($name))
    {
      $name = self::$current;
    }

    return isset(self::$instances[$name]);
  }

  /**
   * Loads the symfony factories.
   */
  public function loadFactories()
  {
    if (sfConfig::get('sf_use_database'))
    {
      // setup our database connections
      $this->factories['databaseManager'] = new sfDatabaseManager($this->configuration, array('auto_shutdown' => false));
    }

    // create a new action stack
    $this->factories['actionStack'] = new sfActionStack();

    // include the factories configuration
    require($this->configuration->getConfigCache()->checkConfig('config/factories.yml'));

    $this->dispatcher->notify(new sfEvent($this, 'context.load_factories'));
  }

  /**
   * Dispatches the current request.
   */
  public function dispatch()
  {
    $this->getController()->dispatch();
  }

  /**
   * Sets the current context to something else
   *
   * @param string $name  The name of the context to switch to
   *
   */
  public static function switchTo($name)
  {
    if (!isset(self::$instances[$name]))
    {
      $currentConfiguration = sfContext::getInstance()->getConfiguration();
      sfContext::createInstance(ProjectConfiguration::getApplicationConfiguration($name, $currentConfiguration->getEnvironment(), $currentConfiguration->isDebug()));
    }

    self::$current = $name;

    sfContext::getInstance()->getConfiguration()->activate();
  }

  /**
   * Returns the configuration instance.
   *
   * @return sfApplicationConfiguration  The current application configuration instance
   */
  public function getConfiguration()
  {
    return $this->configuration;
  }

  /**
   * Retrieves the current event dispatcher.
   *
   * @return sfEventDispatcher An sfEventDispatcher instance
   */
  public function getEventDispatcher()
  {
    return $this->dispatcher;
  }

  /**
   * Retrieve the action name for this context.
   *
   * @return string The currently executing action name, if one is set,
   *                otherwise null.
   */
  public function getActionName()
  {
    // get the last action stack entry
    if ($this->factories['actionStack'] && $lastEntry = $this->factories['actionStack']->getLastEntry())
    {
      return $lastEntry->getActionName();
    }
  }


  /**
   * Retrieve the ActionStack.
   *
   * @return sfActionStack the sfActionStack instance
   */
  public function getActionStack()
  {
    return $this->factories['actionStack'];
  }

  /**
   * Retrieve the controller.
   *
   * @return sfController The current sfController implementation instance.
   */
   public function getController()
   {
     return isset($this->factories['controller']) ? $this->factories['controller'] : null;
   }

   /**
    * Retrieve the logger.
    *
    * @return sfLogger The current sfLogger implementation instance.
    */
   public function getLogger()
   {
     if (!isset($this->factories['logger']))
     {
       $this->factories['logger'] = new sfNoLogger($this->dispatcher);
     }

     return $this->factories['logger'];
   }

  /**
   * Retrieve a database connection from the database manager.
   *
   * This is a shortcut to manually getting a connection from an existing
   * database implementation instance.
   *
   * If the [sf_use_database] setting is off, this will return null.
   *
   * @param  name  $name  A database name.
   *
   * @return mixed A database instance.
   *
   * @throws sfDatabaseException if the requested database name does not exist.
   */
  public function getDatabaseConnection($name = 'default')
  {
    if (!is_null($this->factories['databaseManager']))
    {
      return $this->factories['databaseManager']->getDatabase($name)->getConnection();
    }

    return null;
  }

  public function retrieveObjects($class, $peerMethod)
  {
    $retrievingClass = 'sf'.ucfirst(sfConfig::get('sf_orm', 'propel')).'DataRetriever';

    return call_user_func(array($retrievingClass, 'retrieveObjects'), $class, $peerMethod);
  }

  /**
   * Retrieve the database manager.
   *
   * @return sfDatabaseManager The current sfDatabaseManager instance.
   */
  public function getDatabaseManager()
  {
    return isset($this->factories['databaseManager']) ? $this->factories['databaseManager'] : null;
  }

  /**
   * Retrieve the module directory for this context.
   *
   * @return string An absolute filesystem path to the directory of the
   *                currently executing module, if one is set, otherwise null.
   */
  public function getModuleDirectory()
  {
    // get the last action stack entry
    if (isset($this->factories['actionStack']) && $lastEntry = $this->factories['actionStack']->getLastEntry())
    {
      return sfConfig::get('sf_app_module_dir').'/'.$lastEntry->getModuleName();
    }
  }

  /**
   * Retrieve the module name for this context.
   *
   * @return string The currently executing module name, if one is set,
   *                otherwise null.
   */
  public function getModuleName()
  {
    // get the last action stack entry
    if (isset($this->factories['actionStack']) && $lastEntry = $this->factories['actionStack']->getLastEntry())
    {
      return $lastEntry->getModuleName();
    }
  }

  /**
   * Retrieve the request.
   *
   * @return sfRequest The current sfRequest implementation instance.
   */
  public function getRequest()
  {
    return isset($this->factories['request']) ? $this->factories['request'] : null;
  }

  /**
   * Retrieve the response.
   *
   * @return sfResponse The current sfResponse implementation instance.
   */
  public function getResponse()
  {
    return isset($this->factories['response']) ? $this->factories['response'] : null;
  }

  /**
   * Set the response object.
   *
   * @param sfResponse $response  An sfResponse instance.
   *
   * @return void
   */
  public function setResponse($response)
  {
    $this->factories['response'] = $response;
  }

  /**
   * Retrieve the storage.
   *
   * @return sfStorage The current sfStorage implementation instance.
   */
  public function getStorage()
  {
    return isset($this->factories['storage']) ? $this->factories['storage'] : null;
  }

  /**
   * Retrieve the view cache manager
   *
   * @return sfViewCacheManager The current sfViewCacheManager implementation instance.
   */
  public function getViewCacheManager()
  {
    return isset($this->factories['viewCacheManager']) ? $this->factories['viewCacheManager'] : null;
  }

  /**
   * Retrieve the i18n instance
   *
   * @return sfI18N The current sfI18N implementation instance.
   */
  public function getI18N()
  {
    if (!sfConfig::get('sf_i18n'))
    {
      throw new sfConfigurationException('You must enabled i18n support in your settings.yml configuration file.');
    }

    return $this->factories['i18n'];
  }

  /**
   * Retrieve the routing instance.
   *
   * @return sfRouting The current sfRouting implementation instance.
   */
  public function getRouting()
  {
    return isset($this->factories['routing']) ? $this->factories['routing'] : null;
  }

  /**
   * Retrieve the user.
   *
   * @return sfUser The current sfUser implementation instance.
   */
  public function getUser()
  {
    return isset($this->factories['user']) ? $this->factories['user'] : null;
  }

  /**
   * Returns the configuration cache.
   *
   * @return sfConfigCache A sfConfigCache instance
   */
  public function getConfigCache()
  {
    return $this->configuration->getConfigCache();
  }

  /**
   * Gets an object from the current context.
   *
   * @param  string $name  The name of the object to retrieve
   *
   * @return object The object associated with the given name
   */
  public function get($name)
  {
    if (!$this->has($name))
    {
      throw new sfException(sprintf('The "%s" object does not exist in the current context.', $name));
    }

    return $this->factories[$name];
  }

  /**
   * Puts an object in the current context.
   *
   * @param string $name    The name of the object to store
   * @param object $object  The object to store
   */
  public function set($name, $object)
  {
    $this->factories[$name] = $object;
  }

  /**
   * Returns true if an object is currently stored in the current context with the given name, false otherwise.
   *
   * @param  string $name  The object name
   *
   * @return bool true if the object is not null, false otherwise
   */
  public function has($name)
  {
    return isset($this->factories[$name]);
  }

  /**
   * Listens to the template.filter_parameters event.
   *
   * @param  sfEvent $event       An sfEvent instance
   * @param  array   $parameters  An array of template parameters to filter
   *
   * @return array   The filtered parameters array
   */
  public function filterTemplateParameters(sfEvent $event, $parameters)
  {
    $parameters['sf_context']  = $this;
    $parameters['sf_request']  = $this->factories['request'];
    $parameters['sf_params']   = $this->factories['request']->getParameterHolder();
    $parameters['sf_response'] = $this->factories['response'];
    $parameters['sf_user']     = $this->factories['user'];

    return $parameters;
  }

  /**
   * Execute the shutdown procedure.
   *
   * @return void
   */
  public function shutdown()
  {
    // shutdown all factories
    if($this->has('user'))
    {
      $this->getUser()->shutdown();
      $this->getStorage()->shutdown();
    }

    if ($this->has('routing'))
    {
    	$this->getRouting()->shutdown();
    }

    if (sfConfig::get('sf_use_database'))
    {
      $this->getDatabaseManager()->shutdown();
    }

    if (sfConfig::get('sf_logging_enabled'))
    {
      $this->getLogger()->shutdown();
    }
  }
}
