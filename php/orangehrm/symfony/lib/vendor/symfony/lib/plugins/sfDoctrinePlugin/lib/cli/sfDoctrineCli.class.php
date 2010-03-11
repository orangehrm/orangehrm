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
 * sfDoctrineCli
 *
 * @package    symfony
 * @subpackage doctrine
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfDoctrineCli.class.php 13851 2008-12-08 22:54:16Z Jonathan.Wage $
 */
class sfDoctrineCli extends Doctrine_Cli
{
  protected $dispatcher,
            $formatter;

  /**
   * Set the dispatcher of the cli instance
   *
   * @param object $dispatcher
   * @return void
   */
  public function setDispatcher($dispatcher)
  {
    $this->dispatcher = $dispatcher;
  }

  /**
   * Set the formatter to use for the cli
   *
   * @param object $formatter
   * @return void
   */
  public function setFormatter($formatter)
  {
    $this->formatter = $formatter;
  }

  /**
   * Notify the dispatcher of a message. We silent the messages from the Doctrine cli.
   *
   * @param string $notification
   * @param string $style
   * @return false
   */
  public function notify($notification = null, $style = 'HEADER')
  {
    $this->dispatcher->notify(new sfEvent($this, 'command.log', array($this->formatter->formatSection('doctrine', $notification))));
  }

  /**
   * Notify symfony of an exception thrown by the Doctrine cli
   *
   * @param Doctrine_Exception $exception
   * @return void
   * @throws sfException
   */
  public function notifyException($exception)
  {
    throw new sfException($exception->getMessage());
  }
}