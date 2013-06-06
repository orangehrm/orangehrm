<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(136);

class myLogger extends sfLogger
{
  public $log = '';

  protected function doLog($message, $priority)
  {
    $this->log .= $message;
  }
}

class notaLogger
{
}

$dispatcher = new sfEventDispatcher();
$logger = new myLogger($dispatcher, array('log_dir_name' => '/tmp'));

$options = $logger->getOptions();
$t->is($options['log_dir_name'], '/tmp', '->getOptions() returns the options for the logger instance');

// ->setLogLevel() ->getLogLevel()
$t->diag('->setLogLevel() ->getLogLevel()');
$t->is($logger->getLogLevel(), sfLogger::INFO, '->getLogLevel() gets the current log level');
$logger->setLogLevel(sfLogger::WARNING);
$t->is($logger->getLogLevel(), sfLogger::WARNING, '->setLogLevel() sets the log level');
$logger->setLogLevel('err');
$t->is($logger->getLogLevel(), sfLogger::ERR, '->setLogLevel() accepts a class constant or a string as its argument');

// ->initialize()
$t->diag('->initialize()');
$logger->initialize($dispatcher, array('level' => sfLogger::ERR));
$t->is($logger->getLogLevel(), sfLogger::ERR, '->initialize() takes an array of options as its second argument');

// ::getPriorityName()
$t->diag('::getPriorityName()');
$t->is(sfLogger::getPriorityName(sfLogger::INFO), 'info', '::getPriorityName() returns the name of a priority class constant');
try
{
  sfLogger::getPriorityName(100);
  $t->fail('::getPriorityName() throws an sfException if the priority constant does not exist');
}
catch (sfException $e)
{
  $t->pass('::getPriorityName() throws an sfException if the priority constant does not exist');
}

// ->log()
$t->diag('->log()');
$logger->setLogLevel(sfLogger::DEBUG);
$logger->log('message');
$t->is($logger->log, 'message', '->log() logs a message');

// log level
$t->diag('log levels');
foreach (array('emerg', 'alert', 'crit', 'err', 'warning', 'notice', 'info', 'debug') as $level)
{
  $levelConstant = 'sfLogger::'.strtoupper($level);

  foreach (array('emerg', 'alert', 'crit', 'err', 'warning', 'notice', 'info', 'debug') as $logLevel)
  {
    $logLevelConstant = 'sfLogger::'.strtoupper($logLevel);
    $logger->setLogLevel(constant($logLevelConstant));

    $logger->log = '';
    $logger->log('foo', constant($levelConstant));

    $t->is($logger->log, constant($logLevelConstant) >= constant($levelConstant), sprintf('->log() only logs if the level is >= to the defined log level (%s >= %s)', $logLevelConstant, $levelConstant));
  }
}

// shortcuts
$t->diag('log shortcuts');
foreach (array('emerg', 'alert', 'crit', 'err', 'warning', 'notice', 'info', 'debug') as $level)
{
  $levelConstant = 'sfLogger::'.strtoupper($level);

  foreach (array('emerg', 'alert', 'crit', 'err', 'warning', 'notice', 'info', 'debug') as $logLevel)
  {
    $logger->setLogLevel(constant('sfLogger::'.strtoupper($logLevel)));

    $logger->log = '';
    $logger->log('foo', constant($levelConstant));
    $log1 = $logger->log;

    $logger->log = '';
    $logger->$level('foo');
    $log2 = $logger->log;

    $t->is($log1, $log2, sprintf('->%s($msg) is a shortcut for ->log($msg, %s)', $level, $levelConstant));
  }
}
