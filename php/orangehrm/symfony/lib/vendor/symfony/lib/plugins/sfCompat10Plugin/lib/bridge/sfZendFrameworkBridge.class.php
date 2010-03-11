<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (sfConfig::get('sf_zend_lib_dir'))
{
  set_include_path(sfConfig::get('sf_zend_lib_dir').PATH_SEPARATOR.get_include_path());
}

sfZendFrameworkBridge::requireZendLoader();

/**
 * This class makes easy to use Zend Framework classes within symfony.
 *
 * @package    symfony
 * @subpackage addon
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfZendFrameworkBridge.class.php 5852 2007-11-04 16:09:49Z fabien $
 */
class sfZendFrameworkBridge
{
  static protected
    $withLoader = true;

  public static function autoload($class)
  {
    try
    {
      if (self::$withLoader)
      {
        Zend_Loader::loadClass($class);
      }
      else
      {
        Zend::loadClass($class);
      }
    }
    catch (Zend_Exception $e)
    {
      return false;
    }

    return true;
  }
  
  /**
   * Detect and return the path to current Zend loader class.
   *
   * Starting from ZF 0.9.0 autoloading function has been moved
   * from Zend.php to Zend/Loader.php class.
   * Starting from ZF 1.0.0 Zend.php class no longer exists.
   *
   * This function tries to detect whether Zend_Loader exists
   * and returns its path if yes.
   * If the first step fails, the class will try to find Zend.php library
   * available in ZF <= 0.9.0 and returns its path if its exists.
   *
   * If neither Zend/Loader.php nor Zend.php exists,
   * then this function will raise a sfConfigurationException exception.
   *
   * @return  string  Path to default Zend Loader class
   * @throws  sfConfigurationException
   *
   * @author  Simone Carletti <weppos@weppos.net>
   */
  public static function requireZendLoader()
  {
    // get base path according to sf setting
    $base = sfConfig::get('sf_zend_lib_dir') ? sfConfig::get('sf_zend_lib_dir').'/' : '';

    // first check whether Zend/Loader.php exists
    // Zend/Loader.php is available starting from ZF 0.9.0
    // Before ZF 0.9.0 you should call Zend.php
    // Plese note that Zend.php is still available in ZF 0.9.0
    // but it should not be called because deprecated
    if (file_exists($base.'Zend/Loader.php'))
    {
      require_once($base.'Zend/Loader.php');
      self::$withLoader = true;
    }
    else if (file_exists($base.'Zend.php'))
    {
      require_once($base.'Zend.php');
      self::$withLoader = false;
    }
    else
    {
      throw new sfConfigurationException('Invalid Zend Framework library structure, unable to find Zend/Loader.php (ZF >= 0.9.0) or Zend.php (ZF < 0.9.0) library');
    }
  }
}
