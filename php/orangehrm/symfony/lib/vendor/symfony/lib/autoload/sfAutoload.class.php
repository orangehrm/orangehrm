<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfAutoload class.
 *
 * This class is a singleton as PHP seems to be unable to register 2 autoloaders that are instances
 * of the same class (why?).
 *
 * @package    symfony
 * @subpackage autoload
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfAutoload.class.php 17858 2009-05-01 21:22:50Z FabianLange $
 */
class sfAutoload
{
  static protected
    $freshCache = false,
    $instance   = null;

  protected
    $overriden = array(),
    $classes   = array();

  protected function __construct()
  {
  }

  /**
   * Retrieves the singleton instance of this class.
   *
   * @return sfCoreAutoload A sfCoreAutoload implementation instance.
   */
  static public function getInstance()
  {
    if (!isset(self::$instance))
    {
      self::$instance = new sfAutoload();
    }

    return self::$instance;
  }

  /**
   * Register sfAutoload in spl autoloader.
   *
   * @return void
   */
  static public function register()
  {
    ini_set('unserialize_callback_func', 'spl_autoload_call');

    if (false === spl_autoload_register(array(self::getInstance(), 'autoload')))
    {
      throw new sfException(sprintf('Unable to register %s::autoload as an autoloading method.', get_class(self::getInstance())));
    }
  }

  /**
   * Unregister sfAutoload from spl autoloader.
   *
   * @return void
   */
  static public function unregister()
  {
    spl_autoload_unregister(array(self::getInstance(), 'autoload'));
  }

  public function setClassPath($class, $path)
  {
    $this->overriden[$class] = $path;

    $this->classes[$class] = $path;
  }

  public function getClassPath($class)
  {
    return isset($this->classes[$class]) ? $this->classes[$class] : null;
  }

  public function reloadClasses($force = false)
  {
    // only (re)load the autoloading cache once per request
    if (self::$freshCache)
    {
      return;
    }

    $configuration = sfProjectConfiguration::getActive();
    if (!$configuration || !$configuration instanceof sfApplicationConfiguration)
    {
      return;
    }

    self::$freshCache = true;
    if (file_exists($configuration->getConfigCache()->getCacheName('config/autoload.yml')))
    {
      self::$freshCache = false;
      if ($force)
      {
        unlink($configuration->getConfigCache()->getCacheName('config/autoload.yml'));
      }
    }

    $file = $configuration->getConfigCache()->checkConfig('config/autoload.yml');

    $this->classes = include($file);

    foreach ($this->overriden as $class => $path)
    {
      $this->classes[$class] = $path;
    }
  }

  /**
   * Handles autoloading of classes that have been specified in autoload.yml.
   *
   * @param string $class A class name.
   *
   * @return boolean Returns true if the class has been loaded
   */
  public function autoload($class)
  {
    // load the list of autoload classes
    if (!$this->classes)
    {
      self::reloadClasses();
    }

    return self::loadClass($class);
  }

  public function autoloadAgain($class)
  {
    self::reloadClasses(true);

    return self::loadClass($class);
  }

  /**
   * Tries to load a class that has been specified in autoload.yml.
   *
   * @param string $class A class name.
   *
   * @return boolean Returns true if the class has been loaded
   */
  public function loadClass($class)
  {
    // class already exists
    if (class_exists($class, false) || interface_exists($class, false))
    {
      return true;
    }

    // we have a class path, let's include it
    if (isset($this->classes[$class]))
    {
      require($this->classes[$class]);

      return true;
    }

    // see if the file exists in the current module lib directory
    // must be in a module context
    if (sfContext::hasInstance() && ($module = sfContext::getInstance()->getModuleName()) && isset($this->classes[$module.'/'.$class]))
    {
      require($this->classes[$module.'/'.$class]);

      return true;
    }

    return false;
  }
}
