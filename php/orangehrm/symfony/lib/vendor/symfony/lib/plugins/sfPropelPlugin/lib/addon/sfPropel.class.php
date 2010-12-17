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
 * @package    sfPropelPlugin
 * @subpackage addon
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfPropel.class.php 23737 2009-11-09 23:23:25Z Kris.Wallsmith $
 */
class sfPropel
{
  static protected
    $defaultCulture = 'en';

  /**
   * Initialize symfony propel
   *
   * @param sfEventDispatcher $dispatcher
   * @param string $culture
   * 
   * @deprecated Moved to {@link sfPropelPluginConfiguration}
   */
  static public function initialize(sfEventDispatcher $dispatcher, $culture = null)
  {
    $dispatcher->notify(new sfEvent(__CLASS__, 'application.log', array(__METHOD__.'() has been deprecated. Please call sfPropel::setDefaultCulture() to set the culture.', 'priority' => sfLogger::NOTICE)));

    if (null !== $culture)
    {
      self::setDefaultCulture($culture);
    }
    else if (class_exists('sfContext', false) && sfContext::hasInstance() && $user = sfContext::getInstance()->getUser())
    {
      self::setDefaultCulture($user->getCulture());
    }
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
   * @deprecated Use Propel::importClass() instead
   */
  static public function import($path)
  {
    return Propel::importClass($path);
  }

  /**
   * @deprecated Use Propel::importClass() instead
   */
  static public function importClass($path)
  {
    return Propel::importClass($path);
  }

  /**
   * Clears all instance pools.
   *
   * @deprecated Moved to {@link sfPropelPluginConfiguration}
   */
  static public function clearAllInstancePools()
  {
    sfProjectConfiguration::getActive()->getPluginConfiguration('sfPropelPlugin')->clearAllInstancePools();
  }
}
