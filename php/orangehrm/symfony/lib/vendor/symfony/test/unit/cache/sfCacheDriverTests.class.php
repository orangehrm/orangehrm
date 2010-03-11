<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class sfCacheDriverTests
{
  static public function launch($t, $cache)
  {
    // ->set() ->get() ->has()
    $t->diag('->set() ->get() ->has()');
    $data = 'some random data to store in the cache system... (\'"!#/é$£)';
    $t->ok($cache->set('test', $data, 86400), '->set() returns true if data are stored in cache');
    $t->is($cache->get('test'), $data, '->get() retrieves data form the cache');
    $t->is($cache->has('test'), true, '->has() returns true if the cache exists');

    $t->ok($cache->set('test', $data, -10), '->set() takes a lifetime as its third argument');
    $t->is($cache->get('test', 'default'), 'default', '->get() returns the default value if cache has expired');
    $t->is($cache->has('test'), false, '->has() returns true if the cache exists');

    $t->is($cache->get('foo'), null, '->get() returns null if the cache does not exist');
    $t->is($cache->get('foo', 'default'), 'default', '->get() takes a default value as its second argument');
    $t->is($cache->has('foo'), false, '->has() returns false if the cache does not exist');

    $data = 'another some random data to store in the cache system...';
    $t->ok($cache->set('test', $data), '->set() overrides previous data stored in the cache');
    $t->is($cache->get('test'), $data, '->set() retrieves the latest data form the cache');

    $cache->clean();
    $cache->set('foo', 'foo');
    $cache->set('foo:bar', 'bar');
    $cache->set('foo:bar:foo:bar:foo', 'foobar');
    $t->is($cache->get('foo'), 'foo', '->set() accepts a "namespaced" cache key');
    $t->is($cache->get('foo:bar'), 'bar', '->set() accepts a "namespaced" cache key');
    $t->is($cache->get('foo:bar:foo:bar:foo'), 'foobar', '->set() accepts a "namespaced" cache key');

    // ->clean()
    $t->diag('->clean()');
    $data = 'some random data to store in the cache system...';
    $cache->set('foo', $data, -10);
    $cache->set('bar', $data, 86400);

    $cache->clean(sfCache::OLD);
    $t->is($cache->has('foo'), false, '->clean() cleans old cache key if given the sfCache::OLD argument');
    $t->is($cache->has('bar'), true, '->clean() cleans old cache key if given the sfCache::OLD argument');

    $cache->set('foo', $data, -10);
    $cache->set('bar', $data, 86400);

    $cache->clean(sfCache::ALL);
    $t->is($cache->has('foo'), false, '->clean() cleans all cache key if given the sfCache::ALL argument');
    $t->is($cache->has('bar'), false, '->clean() cleans all cache key if given the sfCache::ALL argument');

    $cache->set('foo', $data, -10);
    $cache->set('bar', $data, 86400);

    $cache->clean();
    $t->is($cache->has('foo'), false, '->clean() cleans all cache key if given no argument');
    $t->is($cache->has('bar'), false, '->clean() cleans all cache key if given no argument');

    $cache->clean();
    $cache->setOption('automatic_cleaning_factor', 1);
    $cache->set('foo', $data);
    $cache->set('foo', $data);
    $cache->set('foo', $data);
    $cache->setOption('automatic_cleaning_factor', 1000);

    // ->remove()
    $t->diag('->remove()');
    $data = 'some random data to store in the cache system...';
    $cache->clean();
    $cache->set('foo', $data);
    $cache->set('bar', $data);

    $cache->remove('foo');
    $t->is($cache->has('foo'), false, '->remove() takes a cache key as its first argument');
    $t->is($cache->get('foo'), null, '->remove() takes a cache key as its first argument');
    $t->is($cache->has('bar'), true, '->remove() takes a cache key as its first argument');

    // ->removePattern()
    $t->diag('->removePattern()');

    $tests = array(
      '*:bar:foo'  => array(false, false, true, true),
      'foo:bar:*'  => array(false, true, false, true),
      'foo:**:foo' => array(false, true, true, true),
      'foo:bar:**' => array(false, true, false, false),
      '**:bar'     => array(true, true, true, false),
      '**'         => array(false, false, false, false),
    );

    foreach ($tests as $pattern => $results)
    {
      $t->diag($pattern);

      $cache->clean();

      $cache->set('foo:bar:foo', 'foo');
      $cache->set('bar:bar:foo', 'foo');
      $cache->set('foo:bar:foo1', 'foo');
      $cache->set('foo:bar:foo:bar', 'foo');

      $cache->removePattern($pattern);

      $t->is($cache->has('foo:bar:foo'), $results[0], '->removePattern() takes a pattern as its first argument');
      $t->is($cache->has('bar:bar:foo'), $results[1], '->removePattern() takes a pattern as its first argument');
      $t->is($cache->has('foo:bar:foo1'), $results[2], '->removePattern() takes a pattern as its first argument');
      $t->is($cache->has('foo:bar:foo:bar'), $results[3], '->removePattern() takes a pattern as its first argument');
    }

    // ->getTimeout()
    $t->diag('->getTimeout()');
    foreach (array(86400, 10) as $lifetime)
    {
      $cache->set('foo', 'bar', $lifetime);

      $delta = $cache->getTimeout('foo') - time();
      $t->ok($delta >= $lifetime - 1 && $delta <= $lifetime, '->getTimeout() returns the timeout time for a given cache key');
    }

    $cache->set('bar', 'foo', -10);
    $t->is($cache->getTimeout('bar'), 0, '->getTimeout() returns the timeout time for a given cache key');

    foreach (array(86400, 10) as $lifetime)
    {
      $cache->setOption('lifetime', $lifetime);
      $cache->set('foo', 'bar');

      $delta = $cache->getTimeout('foo') - time();
      $t->ok($delta >= $lifetime - 1 && $delta <= $lifetime, '->getTimeout() returns the timeout time for a given cache key');
    }

    $t->is($cache->getTimeout('nonexistantkey'), 0, '->getTimeout() returns 0 if the cache key does not exist');

    // ->getLastModified()
    $t->diag('->getLastModified()');
    foreach (array(86400, 10) as $lifetime)
    {
      $cache->set('bar', 'foo', $lifetime);
      $now = time();
      $lastModified = $cache->getLastModified('bar');
      $t->ok($lastModified >= time() - 1 && $lastModified <= time(), '->getLastModified() returns the last modified time for a given cache key');
    }

    $cache->set('bar', 'foo', -10);
    $t->is($cache->getLastModified('bar'), 0, '->getLastModified() returns the last modified time for a given cache key');

    foreach (array(86400, 10) as $lifetime)
    {
      $cache->setOption('lifetime', $lifetime);
      $cache->set('bar', 'foo');

      $now = time();
      $lastModified = $cache->getLastModified('bar');
      $t->ok($lastModified >= time() - 1 && $lastModified <= time(), '->getLastModified() returns the last modified time for a given cache key');
    }

    $t->is($cache->getLastModified('nonexistantkey'), 0, '->getLastModified() returns 0 if the cache key does not exist');

    // ->getMany()
    $t->diag('->getMany()');
    $cache->clean();

    $cache->set('bar', 'foo');
    $cache->set('foo', 'bar');

    $t->is($cache->getMany(array('foo', 'bar')), array('foo' => 'bar', 'bar' => 'foo'), '->getMany() gets many keys in one call');

    $cache->clean();
  }
}
