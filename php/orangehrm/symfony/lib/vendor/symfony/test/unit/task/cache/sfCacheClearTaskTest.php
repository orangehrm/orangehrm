<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../../bootstrap/task.php');

$t = new lime_test(2);

$dispatcher = new sfEventDispatcher();
$formatter = new sfFormatter();

$task = new sfGenerateProjectTask($dispatcher, $formatter);
$task->run(array('test'));
$task = new sfGenerateAppTask($dispatcher, $formatter);
$task->run(array('frontend'));

require_once sfConfig::get('sf_root_dir').'/config/ProjectConfiguration.class.php';
$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'test', true);

// Put something in the cache
$file = sfConfig::get('sf_config_cache_dir').DIRECTORY_SEPARATOR.'test';
touch($file);

$t->ok(file_exists($file), 'The test file is in the cache');

$task = new sfCacheClearTask($dispatcher, $formatter);
$task->run();

$t->ok(!file_exists($file), 'The test file is removed by the cache:clear task');
