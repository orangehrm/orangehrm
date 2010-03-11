<?php

/*
 * This file is part of the symfony package.
 * (c) Jonathan H. Wage <jonwage@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfDoctrinePluginConfiguration Class
 *
 * @package    symfony
 * @subpackage doctrine
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfDoctrineConnectionListener.class.php 11878 2008-09-30 20:14:40Z Jonathan.Wage $
 */
class sfDoctrinePluginConfiguration extends sfPluginConfiguration
{
  public function initialize()
  {
    require_once dirname(__FILE__) . '/config.php';

    return true;
  }
}