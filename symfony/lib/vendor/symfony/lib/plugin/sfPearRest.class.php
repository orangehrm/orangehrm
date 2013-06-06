<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once 'PEAR/REST.php';

/**
 * sfPearRest interacts with a PEAR channel.
 *
 * @package    symfony
 * @subpackage plugin
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfPearRest.class.php 10677 2008-08-05 19:11:48Z fabien $
 */
class sfPearRest extends PEAR_REST
{
  /**
   * @see PEAR_REST::downloadHttp()
   */
  public function downloadHttp($url, $lastmodified = null, $accept = false)
  {
    return parent::downloadHttp($url, $lastmodified, array_merge(false !== $accept ? $accept : array(), array("\r\nX-SYMFONY-VERSION: ".SYMFONY_VERSION)));
  }
}
