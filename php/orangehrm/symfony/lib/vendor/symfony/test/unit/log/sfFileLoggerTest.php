<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(7);

require_once(dirname(__FILE__).'/../../../lib/util/sfToolkit.class.php');
$file = sys_get_temp_dir().DIRECTORY_SEPARATOR.'sf_log_file.txt';
if (file_exists($file))
{
  unlink($file);
}

$dispatcher = new sfEventDispatcher();

// ->initialize()
$t->diag('->initialize()');
try
{
  $logger = new sfFileLogger($dispatcher);
  $t->fail('->initialize() parameters must contains a "file" parameter');
}
catch (sfConfigurationException $e)
{
  $t->pass('->initialize() parameters must contains a "file" parameter');
}

// ->log()
$t->diag('->log()');
$logger = new sfFileLogger($dispatcher, array('file' => $file));
$logger->log('foo');
$lines = explode("\n", file_get_contents($file));
$t->like($lines[0], '/foo/', '->log() logs a message to the file');
$logger->log('bar');
$lines = explode("\n", file_get_contents($file));
$t->like($lines[1], '/bar/', '->log() logs a message to the file');

class TestLogger extends sfFileLogger
{
  public function getTimeFormat()
  {
    return $this->timeFormat;
  }

  protected function getPriority($priority)
  {
    return '*'.$priority.'*';
  }
}

// option: format
$t->diag('option: format');
unlink($file);
$logger = new TestLogger($dispatcher, array('file' => $file));
$logger->log('foo');
$t->is(file_get_contents($file), strftime($logger->getTimeFormat()).' symfony [*6*] foo'.PHP_EOL, '->initialize() can take a format option');

unlink($file);
$logger = new TestLogger($dispatcher, array('file' => $file, 'format' => '%message%'));
$logger->log('foo');
$t->is(file_get_contents($file), 'foo', '->initialize() can take a format option');

// option: time_format
$t->diag('option: time_format');
unlink($file);
$logger = new TestLogger($dispatcher, array('file' => $file, 'time_format' => '%Y %m %d'));
$logger->log('foo');
$t->is(file_get_contents($file), strftime($logger->getTimeFormat()).' symfony [*6*] foo'.PHP_EOL, '->initialize() can take a format option');

// option: type
$t->diag('option: type');
unlink($file);
$logger = new TestLogger($dispatcher, array('file' => $file, 'type' => 'foo'));
$logger->log('foo');
$t->is(file_get_contents($file), strftime($logger->getTimeFormat()).' foo [*6*] foo'.PHP_EOL, '->initialize() can take a format option');

// ->shutdown()
$t->diag('->shutdown()');
$logger->shutdown();

unlink($file);
