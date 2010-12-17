<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');
require_once(dirname(__FILE__).'/sfCacheDriverTests.class.php');

$t = new lime_test(15);

class sfSimpleCache extends sfCache
{
  public $data = array();

  public function get($key, $default = null)
  {
    return isset($this->data[$key]) ? $this->data[$key] : $default;
  }

  public function set($key, $data, $lifetime = null)
  {
    $this->data[$key] = $data;
  }

  public function remove($key)
  {
    unset($this->data[$key]);
  }

  public function removePattern($pattern, $delimiter = ':')
  {
    $this->data = array();
  }

  public function has($key)
  {
    return isset($this->data[$key]);
  }

  public function clean($mode = sfCache::ALL)
  {
    $this->data = array();
  }

  public function getLastModified($key)
  {
    return 0;
  }

  public function getTimeout($key)
  {
    return 0;
  }
}

class testFunctionCache
{
  static $count = 0;

  static function test($arg1, $arg2)
  {
    ++self::$count;

    return $arg1.$arg2;
  }
}

$count = 0;
function testFunctionCache($arg1, $arg2)
{
  global $count;

  ++$count;

  return $arg1.$arg2;
}

// ->call()
$t->diag('->call()');

$cache = new sfSimpleCache();
$functionCache = new sfFunctionCache($cache);
$result = testFunctionCache(1, 2);
$t->is($count, 1);
$t->is($functionCache->call('testFunctionCache', array(1, 2)), $result, '->call() works with functions');
$t->is($count, 2);
$t->is($functionCache->call('testFunctionCache', array(1, 2)), $result, '->call() stores the function call in cache');
$t->is($count, 2);

$result = testFunctionCache::test(1, 2);
$t->is(testFunctionCache::$count, 1);
$t->is($functionCache->call(array('testFunctionCache', 'test'), array(1, 2)), $result, '->call() works with static method calls');
$t->is(testFunctionCache::$count, 2);
$t->is($functionCache->call(array('testFunctionCache', 'test'), array(1, 2)), $result, '->call() stores the function call in cache');
$t->is(testFunctionCache::$count, 2);

testFunctionCache::$count = 0;
$object = new testFunctionCache();
$result = $object->test(1, 2);
$t->is(testFunctionCache::$count, 1);
$t->is($functionCache->call(array($object, 'test'), array(1, 2)), $result, '->call() works with object methods');
$t->is(testFunctionCache::$count, 2);
$t->is($functionCache->call(array($object, 'test'), array(1, 2)), $result, '->call() stores the function call in cache');
$t->is(testFunctionCache::$count, 2);
