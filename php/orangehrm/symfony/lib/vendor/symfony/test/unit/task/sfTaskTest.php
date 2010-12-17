<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
require_once dirname(__FILE__).'/../../bootstrap/unit.php';

$t = new lime_test(15);

abstract class BaseTestTask extends sfTask
{
  public
    $lastArguments = array(),
    $lastOptions   = array();

  public function __construct()
  {
    // lazy constructor
    parent::__construct(new sfEventDispatcher(), new sfFormatter());
  }

  protected function execute($arguments = array(), $options = array())
  {
    $this->lastArguments = $arguments;
    $this->lastOptions = $options;
  }
}

// ->run()
$t->diag('->run()');

class ArgumentsTest1Task extends BaseTestTask
{
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('foo', sfCommandArgument::REQUIRED),
      new sfCommandArgument('bar', sfCommandArgument::OPTIONAL),
    ));
  }
}

$task = new ArgumentsTest1Task();
$task->run(array('FOO'));
$t->is_deeply($task->lastArguments, array('foo' => 'FOO', 'bar' => null), '->run() accepts an indexed array of arguments');

$task->run(array('foo' => 'FOO'));
$t->is_deeply($task->lastArguments, array('foo' => 'FOO', 'bar' => null), '->run() accepts an associative array of arguments');

$task->run(array('bar' => 'BAR', 'foo' => 'FOO'));
$t->is_deeply($task->lastArguments, array('foo' => 'FOO', 'bar' => 'BAR'), '->run() accepts an unordered associative array of arguments');

$task->run('FOO BAR');
$t->is_deeply($task->lastArguments, array('foo' => 'FOO', 'bar' => 'BAR'), '->run() accepts a string of arguments');

$task->run(array('foo' => 'FOO', 'bar' => null));
$t->is_deeply($task->lastArguments, array('foo' => 'FOO', 'bar' => null), '->run() accepts an associative array of arguments when optional arguments are passed as null');

$task->run(array('bar' => null, 'foo' => 'FOO'));
$t->is_deeply($task->lastArguments, array('foo' => 'FOO', 'bar' => null), '->run() accepts an unordered associative array of arguments when optional arguments are passed as null');

class ArgumentsTest2Task extends BaseTestTask
{
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('foo', sfCommandArgument::OPTIONAL | sfCommandArgument::IS_ARRAY),
    ));
  }
}

$task = new ArgumentsTest2Task();
$task->run(array('arg1', 'arg2', 'arg3'));
$t->is_deeply($task->lastArguments, array('foo' => array('arg1', 'arg2', 'arg3')), '->run() accepts an indexed array of an IS_ARRAY argument');

$task->run(array('foo' => array('arg1', 'arg2', 'arg3')));
$t->is_deeply($task->lastArguments, array('foo' => array('arg1', 'arg2', 'arg3')), '->run() accepts an associative array of an IS_ARRAY argument');

class OptionsTest1Task extends BaseTestTask
{
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('none', null, sfCommandOption::PARAMETER_NONE),
      new sfCommandOption('required', null, sfCommandOption::PARAMETER_REQUIRED),
      new sfCommandOption('optional', null, sfCommandOption::PARAMETER_OPTIONAL),
      new sfCommandOption('array', null, sfCommandOption::PARAMETER_REQUIRED | sfCommandOption::IS_ARRAY),
    ));
  }
}

$task = new OptionsTest1Task();
$task->run();
$t->is_deeply($task->lastOptions, array('none' => false, 'required' => null, 'optional' => null, 'array' => array()), '->run() sets empty option values');

$task->run(array(), array('--none', '--required=TEST1', '--array=one', '--array=two', '--array=three'));
$t->is_deeply($task->lastOptions, array('none' => true, 'required' => 'TEST1', 'optional' => null, 'array' => array('one', 'two', 'three')), '->run() accepts an indexed array of option values');

$task->run(array(), array('none', 'required=TEST1', 'array=one', 'array=two', 'array=three'));
$t->is_deeply($task->lastOptions, array('none' => true, 'required' => 'TEST1', 'optional' => null, 'array' => array('one', 'two', 'three')), '->run() accepts an indexed array of unflagged option values');

$task->run(array(), array('none' => false, 'required' => 'TEST1', 'array' => array('one', 'two', 'three')));
$t->is_deeply($task->lastOptions, array('none' => false, 'required' => 'TEST1', 'optional' => null, 'array' => array('one', 'two', 'three')), '->run() accepts an associative array of option values');

$task->run(array(), array('optional' => null, 'array' => array()));
$t->is_deeply($task->lastOptions, array('none' => false, 'required' => null, 'optional' => null, 'array' => array()), '->run() accepts an associative array of options when optional values are passed as empty');

$task->run('--none --required=TEST1 --array=one --array=two --array=three');
$t->is_deeply($task->lastOptions, array('none' => true, 'required' => 'TEST1', 'optional' => null, 'array' => array('one', 'two', 'three')), '->run() accepts a string of options');

$task->run(array(), array('array' => 'one'));
$t->is_deeply($task->lastOptions, array('none' => false, 'required' => null, 'optional' => null, 'array' => array('one')), '->run() accepts an associative array of options with a scalar array option value');
