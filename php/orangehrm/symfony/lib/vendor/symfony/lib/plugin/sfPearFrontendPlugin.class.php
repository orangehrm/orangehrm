<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once 'PEAR/Frontend.php';
require_once 'PEAR/Frontend/CLI.php';

/**
 * The PEAR Frontend object.
 *
 * @package    symfony
 * @subpackage plugin
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfPearFrontendPlugin.class.php 9131 2008-05-21 04:12:00Z Carl.Vondrick $
 */
class sfPearFrontendPlugin extends PEAR_Frontend_CLI
{
  protected
    $dispatcher = null;

  /**
   * Sets the sfEventDispatcher object for this frontend.
   *
   * @param sfEventDispatcher $dispatcher The sfEventDispatcher instance
   */
  public function setEventDispatcher(sfEventDispatcher $dispatcher)
  {
    $this->dispatcher = $dispatcher;
  }

  public function _displayLine($text)
  {
    $this->_display($text);
  }

  public function _display($text)
  {
    $this->dispatcher->notify(new sfEvent($this, 'application.log', $this->splitLongLine($text)));
  }

  protected function splitLongLine($text)
  {
    $lines = '';
    foreach (explode("\n", $text) as $longline)
    {
      foreach (explode("\n", wordwrap($longline, 62)) as $line)
      {
        if ($line = trim($line))
        {
          $lines[] = $line;
        }
      }
    }

    return $lines;
  }
}
