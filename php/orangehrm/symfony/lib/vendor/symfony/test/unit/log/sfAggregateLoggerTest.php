<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(6, new lime_output_color());

$dispatcher = new sfEventDispatcher();

require_once(dirname(__FILE__).'/../../../lib/util/sfToolkit.class.php');
$file = sfToolkit::getTmpDir().DIRECTORY_SEPARATOR.'sf_log_file.txt';
if (file_exists($file))
{
  unlink($file);
}
$fileLogger = new sfFileLogger($dispatcher, array('file' => $file));
$buffer = fopen('php://memory', 'rw');
$streamLogger = new sfStreamLogger($dispatcher, array('stream' => $buffer));

// ->initialize()
$t->diag('->initialize()');
$logger = new sfAggregateLogger($dispatcher, array('loggers' => $fileLogger));
$t->is($logger->getLoggers(), array($fileLogger), '->initialize() can take a "loggers" parameter');

$logger = new sfAggregateLogger($dispatcher, array('loggers' => array($fileLogger, $streamLogger)));
$t->is($logger->getLoggers(), array($fileLogger, $streamLogger), '->initialize() can take a "loggers" parameter');

// ->log()
$t->diag('->log()');
$logger->log('foo');
rewind($buffer);
$content = stream_get_contents($buffer);
$lines = explode("\n", file_get_contents($file));
$t->like($lines[0], '/foo/', '->log() logs a message to all loggers');
$t->is($content, 'foo'.PHP_EOL, '->log() logs a message to all loggers');

// ->getLoggers() ->addLoggers() ->addLogger()
$logger = new sfAggregateLogger($dispatcher);
$logger->addLogger($fileLogger);
$t->is($logger->getLoggers(), array($fileLogger), '->addLogger() adds a new sfLogger instance');

$logger = new sfAggregateLogger($dispatcher);
$logger->addLoggers(array($fileLogger, $streamLogger));
$t->is($logger->getLoggers(), array($fileLogger, $streamLogger), '->addLoggers() adds an array of sfLogger instances');

// ->shutdown()
$t->diag('->shutdown()');
$logger->shutdown();

unlink($file);
