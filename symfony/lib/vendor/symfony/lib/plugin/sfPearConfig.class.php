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
 * @version    SVN: $Id: sfPearConfig.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
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
