<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once 'PEAR/Config.php';

/**
 * sfPearConfig.
 *
 * @package    symfony
 * @subpackage plugin
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfPearDownloader.class.php 5250 2007-09-24 08:11:50Z fabien $
 */
class sfPearConfig extends PEAR_Config
{
  function &getREST($version, $options = array())
  {
    $class = 'sfPearRest'.str_replace('.', '', $version);

    $remote = new $class($this, $options);

    return $remote;
  }
}
