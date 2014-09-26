<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$plan = 73;
$t = new lime_test($plan);

if (!class_exists('Memcache'))
{
  $t->skip('Memcache needed to run these tests', $plan);
  return;
}

require_once(dirname(__FILE__).'/sfCacheDriverTests.class.php');

// setup
sfConfig::set('sf_logging_enabled', false);

// ->initialize()
$t->diag('->initialize()');
try
{
  $cache = new sfMemcacheCache(array('storeCacheInfo' => true));
}
catch (sfInitializationException $e)
{
  $t->skip('Memcached must be active to run these tests', $plan);
  return;
}

sfCacheDriverTests::launch($t, $cache);

// ->remove() test for ticket #6220
$t->diag('->remove() test for ticket #6220');
$backend = $cache->getBackend();
$prefix = $cache->getOption('prefix');
$cache->clean();
$cache->set('test_1', 'abc');
$cache->set('test_2', 'abc');
$cache->remove('test_1');
$cacheInfo = $backend->get($prefix.'_metadata');
$t->ok(is_array($cacheInfo),'Cache info is an array');
$t->is(count($cacheInfo),1,'Cache info contains 1 element');
$t->ok(!in_array($prefix.'test_1',$cacheInfo),'Cache info no longer contains the removed key');
$t->ok(in_array($prefix.'test_2',$cacheInfo),'Cache info still contains the key that was not removed');

// ->removePattern() test for ticket #6220
$t->diag('->removePattern() test for ticket #6220');
$backend = $cache->getBackend();
$prefix = $cache->getOption('prefix');
$cache->clean();
$cache->set('test_1', 'abc');
$cache->set('test_2', 'abc');
$cache->set('test3', 'abc');
$cache->removePattern('test_*');
$cacheInfo = $backend->get($prefix.'_metadata');
$t->ok(is_array($cacheInfo),'Cache info is an array');
$t->is(count($cacheInfo),1,'Cache info contains 1 element');
$t->ok(!in_array($prefix.'test_1',$cacheInfo),'Cache info no longer contains the key that matches the pattern (first key)');
$t->ok(!in_array($prefix.'test_2',$cacheInfo),'Cache info no longer contains the key that matches the pattern (second key)');
$t->ok(in_array($prefix.'test3',$cacheInfo),'Cache info still contains the key that did not match the pattern (third key)');