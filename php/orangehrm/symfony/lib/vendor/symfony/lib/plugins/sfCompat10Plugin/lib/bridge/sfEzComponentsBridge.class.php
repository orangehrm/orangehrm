<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$sf_ez_lib_dir = sfConfig::get('sf_ez_lib_dir') ? sfConfig::get('sf_ez_lib_dir').'/' : '';

if (file_exists($sf_ez_lib_dir.'Base/src/base.php'))
{
  // svn installation
  require_once($sf_ez_lib_dir.'Base/src/base.php');
} 
elseif (file_exists($sf_ez_lib_dir.'Base/base.php'))
{
  // pear installation
  require_once($sf_ez_lib_dir.'Base/base.php');
}
else
{
  throw new sfAutoloadException('Invalid eZ component library path.');
}

/**
 * This class makes easy to use ez components classes within symfony
 *
 * @package    symfony
 * @subpackage addon
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfEzComponentsBridge.class.php 5363 2007-10-04 06:40:33Z noel $
 */
class sfEzComponentsBridge
{
  public static function autoload($class)
  {
    return ezcBase::autoload($class);
  }
}
