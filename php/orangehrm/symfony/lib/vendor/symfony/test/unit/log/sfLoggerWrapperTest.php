<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(2, new lime_output_color());

class myLogger implements sfLoggerInterface
{
  public $log = '';

  public function log($message, $priority = null)
  {
    $this->log = $message;
  }
}

class myLoggerWrapper extends sfLoggerWrapper
{
  public function getLogger()
  {
    return $this->logger;
  }
}

$myLogger = new myLogger();

// __construct()
$t->diag('__construct()');
$logger = new myLoggerWrapper($myLogger);
$t->is($logger->getLogger(), $myLogger, '__construct() takes a logger that implements sfLoggerInterface as its argument');

// ->log()
$t->diag('->log()');
$logger->log('foo');
$t->is($myLogger->log, 'foo', '->log() logs a message with the wrapped logger');
