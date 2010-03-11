<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfLoader is a class which contains the logic to look for files/classes in symfony.
 *
 * This class is deprecated. The same methods now exist in sfApplicationConfiguration.
 *
 * @package    symfony
 * @subpackage util
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfLoader.class.php 17858 2009-05-01 21:22:50Z FabianLange $
 */
class sfLoader
{
  /**
   * Gets the helper directories for a given module name.
   *
   * @param string $moduleName The module name
   *
   * @return array An array of directories
   */
  static public function getHelperDirs($moduleName = '')
  {
    $configuration = sfProjectConfiguration::getActive();

    $configuration->getEventDispatcher()->notify(new sfEvent(null, 'application.log', array('The sfLoader::getHelperDirs() method is deprecated. Please use the same method from sfApplicationConfiguration.', 'priority' => sfLogger::ERR)));

    return $configuration->getHelperDirs($moduleName);
  }

  /**
   * Loads helpers.
   *
   * @param array  $helpers    An array of helpers to load
   * @param string $moduleName A module name (optional)
   *
   * @throws sfViewException
   */
  static public function loadHelpers($helpers, $moduleName = '')
  {
    $configuration = sfProjectConfiguration::getActive();

    $configuration->getEventDispatcher()->notify(new sfEvent(null, 'application.log', array('The sfLoader::loadHelpers() method is deprecated. Please use the same method from sfApplicationConfiguration.', 'priority' => sfLogger::ERR)));

    return $configuration->loadHelpers($helpers, $moduleName);
  }
}
