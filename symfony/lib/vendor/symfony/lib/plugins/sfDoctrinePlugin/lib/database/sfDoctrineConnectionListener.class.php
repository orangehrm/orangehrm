<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) Jonathan H. Wage <jonwage@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Standard connection listener
 *
 * @package    symfony
 * @subpackage doctrine
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfDoctrineConnectionListener.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class sfDoctrineConnectionListener extends Doctrine_EventListener
{
  public function __construct($connection, $encoding)
  {
    $this->connection = $connection;
    $this->encoding = $encoding;
  }

  public function postConnect(Doctrine_Event $event)
  {
    $this->connection->setCharset($this->encoding);
    $this->connection->setDateFormat();
  }
}