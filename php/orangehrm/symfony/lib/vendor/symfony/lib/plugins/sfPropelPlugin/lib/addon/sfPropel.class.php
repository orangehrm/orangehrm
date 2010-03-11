<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Initialization for propel and i18n propel integration.
 *
 * @package    symfony
 * @subpackage propel
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfPropel.class.php 14025 2008-12-14 15:41:43Z Kris.Wallsmith $
 */
class sfPropel
{
  static protected
    $initialized    = false,
    $defaultCulture = 'en';

  /**
   * Initialize sfymfony propel
   *
   * @param sfEventDispatcher $dispatcher
   * @param string $culture
   */
  static public function initialize(sfEventDispatcher $dispatcher, $culture = null)
  {
    if (sfConfig::get('sf_debug') && sfConfig::get('sf_logging_enabled'))
    {
      // add propel logger
      Propel::setLogger(new sfPropelLogger($dispatcher));
    }

    // propel initialization
    $configuration = sfPropelDatabase::getConfiguration();
    if($configuration)
    {
      Propel::setConfiguration($configuration);

      if(!Propel::isInit())
      {
        Propel::initialize();
      }
    }

    $dispatcher->connect('user.change_culture', array('sfPropel', 'listenToChangeCultureEvent'));

    if (!is_null($culture))
    {
      self::setDefaultCulture($culture);
    }
    else if (class_exists('sfContext', false) && sfContext::hasInstance() && $user = sfContext::getInstance()->getUser())
    {
      self::setDefaultCulture($user->getCulture());
    }

    self::$initialized = true;
  }

  /**
   * Sets the default culture
   *
   * @param string $culture
   */
  static public function setDefaultCulture($culture)
  {
    self::$defaultCulture = $culture;
  }

  /**
   * Return the default culture
   *
   * @return string the default culture
   */
  static public function getDefaultCulture()
  {
    if (!self::$initialized && class_exists('sfProjectConfiguration', false))
    {
      self::initialize(sfProjectConfiguration::getActive()->getEventDispatcher());
    }

    return self::$defaultCulture;
  }

  /**
   * Listens to the user.change_culture event.
   *
   * @param sfEvent An sfEvent instance
   *
   */
  static public function listenToChangeCultureEvent(sfEvent $event)
  {
    self::setDefaultCulture($event['culture']);
  }

  /**
   * Include once a file specified in DOT notation and return unqualified classname.
   *
   * This method is the same as in Propel::import().
   * The only difference is that this one takes the autoloading into account.
   *
   * @see Propel::import()
   */
  public static function import($path)
  {
    // extract classname
    if (($pos = strrpos($path, '.')) === false)
    {
      $class = $path;
    }
    else
    {
      $class = substr($path, $pos + 1);
    }

    // check if class exists
    if (class_exists($class, true))
    {
      return $class;
    }

    // turn to filesystem path
    $path = strtr($path, '.', DIRECTORY_SEPARATOR).'.php';

    // include class
    $ret = include_once($path);
    if ($ret === false)
    {
      throw new PropelException("Unable to import class: ".$class." from ".$path);
    }

    // return qualified name
    return $class;
  }

  /**
   * Clears all instance pools.
   */
  static public function clearAllInstancePools()
  {
    $files = sfFinder::type('file')->name('*MapBuilder.php')->in(sfProjectConfiguration::getActive()->getModelDirs());
    foreach ($files as $file)
    {
      $omClass = basename($file, 'MapBuilder.php');
      if (class_exists($omClass) && is_subclass_of($omClass, 'BaseObject'))
      {
        $peer = constant($omClass.'::PEER');
        call_user_func(array($peer, 'clearInstancePool'));
      }
    }
  }
}
