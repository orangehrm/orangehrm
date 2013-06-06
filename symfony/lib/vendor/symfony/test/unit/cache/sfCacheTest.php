<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

class myCache extends sfCache
{
  public function get($key, $default = null) {}
  public function has($key) {}
  public function set($key, $data, $lifetime = null) {}
  public function remove($key) {}
  public function clean($mode = sfCache::ALL) {}
  public function getTimeout($key) {}
  public function getLastModified($key) {}
  public function removePattern($pattern, $delimiter = ':') {}
}

class fakeCache
{
}

$t = new lime_test(1);

// ->initialize()
$t->diag('->initialize()');
$cache = new myCache();
$cache->initialize(array('foo' => 'bar'));
$t->is($cache->getOption('foo'), 'bar', '->initialize() takes an array of options as its first argument');
