<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once dirname(__FILE__).'/../../bootstrap/unit.php';

$rootDir = realpath(dirname(__FILE__).'/../../functional/fixtures');
$pluginRoot = realpath($rootDir.'/plugins/sfAutoloadPlugin');

require_once $pluginRoot.'/config/sfAutoloadPluginConfiguration.class.php';

$t = new lime_test(9);

class ProjectConfiguration extends sfProjectConfiguration
{
  public function setup()
  {
    $this->enablePlugins('sfAutoloadPlugin');
  }
}

// ->guessRootDir() ->guessName()
$t->diag('->guessRootDir() ->guessName()');

$configuration = new sfProjectConfiguration($rootDir);
$pluginConfig = new sfAutoloadPluginConfiguration($configuration);

$t->is($pluginConfig->getRootDir(), $pluginRoot, '->guessRootDir() guesses plugin root directory');
$t->is($pluginConfig->getName(), 'sfAutoloadPlugin', '->guessName() guesses plugin name');

// ->filterTestFiles()
$t->diag('->filterTestFiles()');

// test:all
$task = new sfTestAllTask($configuration->getEventDispatcher(), new sfFormatter());
$event = new sfEvent($task, 'task.test.filter_test_files', array('arguments' => array(), 'options' => array()));
$files = $pluginConfig->filterTestFiles($event, array());
$t->is(count($files), 6, '->filterTestFiles() adds all plugin tests');

// test:functional
$task = new sfTestFunctionalTask($configuration->getEventDispatcher(), new sfFormatter());
$event = new sfEvent($task, 'task.test.filter_test_files', array('arguments' => array('controller' => array()), 'options' => array()));
$files = $pluginConfig->filterTestFiles($event, array());
$t->is(count($files), 3, '->filterTestFiles() adds functional plugin tests');

$task = new sfTestFunctionalTask($configuration->getEventDispatcher(), new sfFormatter());
$event = new sfEvent($task, 'task.test.filter_test_files', array('arguments' => array('controller' => array('BarFunctional')), 'options' => array()));
$files = $pluginConfig->filterTestFiles($event, array());
$t->is(count($files), 1, '->filterTestFiles() adds functional plugin tests when a controller is specified');

$task = new sfTestFunctionalTask($configuration->getEventDispatcher(), new sfFormatter());
$event = new sfEvent($task, 'task.test.filter_test_files', array('arguments' => array('controller' => array('nested/NestedFunctional')), 'options' => array()));
$files = $pluginConfig->filterTestFiles($event, array());
$t->is(count($files), 1, '->filterTestFiles() adds functional plugin tests when a nested controller is specified');

// test:unit
$task = new sfTestUnitTask($configuration->getEventDispatcher(), new sfFormatter());
$event = new sfEvent($task, 'task.test.filter_test_files', array('arguments' => array('name' => array()), 'options' => array()));
$files = $pluginConfig->filterTestFiles($event, array());
$t->is(count($files), 3, '->filterTestFiles() adds unit plugin tests');

$task = new sfTestUnitTask($configuration->getEventDispatcher(), new sfFormatter());
$event = new sfEvent($task, 'task.test.filter_test_files', array('arguments' => array('name' => array('FooUnit')), 'options' => array()));
$files = $pluginConfig->filterTestFiles($event, array());
$t->is(count($files), 1, '->filterTestFiles() adds unit plugin tests when a name is specified');

$task = new sfTestUnitTask($configuration->getEventDispatcher(), new sfFormatter());
$event = new sfEvent($task, 'task.test.filter_test_files', array('arguments' => array('name' => array('nested/NestedUnit')), 'options' => array()));
$files = $pluginConfig->filterTestFiles($event, array());
$t->is(count($files), 1, '->filterTestFiles() adds unit plugin tests when a nested name is specified');
