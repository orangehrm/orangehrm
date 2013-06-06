<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfLoggerInterface is the interface all symfony loggers must implement.
 *
 * @package    symfony
 * @subpackage log
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfLoggerInterface.class.php 9081 2008-05-20 00:47:12Z Carl.Vondrick $
 */
interface sfLoggerInterface
{
  /**
   * Logs a message.
   *
   * @param string $message   Message
   * @param string $priority  Message priority
   */
  public function log($message, $priority = null);
}
