<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfProcessCache stores content in memory if you run a PHP accelerator.
 *
 * Current PHP accelerator supported: APC, XCache and Eaccelerator.
 *
 * @package    symfony
 * @subpackage cache
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id$
 */
class sfProcessCache
{
  /**
   * Gets the cache engine name or false if no PHP accelerator is enabled.
   *
   * @return string The cache engine name
   */
  public static function cacher()
  {
    static $cacher = null;

    if (null === $cacher)
    {
      if (!sfConfig::get('sf_use_process_cache'))
      {
        $cacher = false;
      }
      elseif (function_exists('apc_store'))
      {
        $cacher = 'apc';
      }
      elseif (function_exists('xcache_set'))
      {
        $cacher = 'xcache';
      }
      elseif (function_exists('eaccelerator_put'))
      {
        $cacher = 'eaccelerator';
      }
      else
      {
        $cacher = false;
      }
    }

    return $cacher;
  }

  /**
   * Gets the prefix to use for all key name.
   *
   * @return string The prefix string
   */
  public static function getPrefix()
  {
    static $prefix = null;

    if (!$prefix)
    {
      $prefix = md5(sfConfig::get('sf_app_dir')).'_';
    }

    return $prefix;
  }

  /**
   * Sets a value in the cache for the specified key.
   *
   * @param string The key name
   * @param string The content to put in cache
   * @param int The life time to keep the content in the cache in seconds
   *
   * @return boolean true if ok
   */
  public static function set($key, $value, $lifeTime = 0)
  {
    switch (self::cacher())
    {
      case 'apc':
        return apc_store(self::getPrefix().$key, $value, $lifeTime);
      case 'xcache':
        return xcache_set(self::getPrefix().$key, $value, $lifeTime);
      case 'eaccelerator':
        return eaccelerator_put(self::getPrefix().$key, serialize($value), $lifeTime);
    }

    return false;
  }

  /**
   * Gets a value in the cache for the specified key.
   *
   * @param string The key name
   *
   * @return mixed The content associated with the key or null if the key does not exist
   */
  public static function get($key)
  {
    switch (self::cacher())
    {
      case 'apc':
        $value = apc_fetch(self::getPrefix().$key);
        return false === $value ? null : $value;
      case 'xcache':
        return xcache_isset(self::getPrefix().$key) ? xcache_get(self::getPrefix().$key) : null;
      case 'eaccelerator':
        return unserialize(eaccelerator_get(self::getPrefix().$key));
    }

    return null;
  }

  /**
   * Returns true if a given key exists in the cache, false otherwise.
   *
   * @param string The key name
   *
   * @return boolean true if the key exists, false otherwise
   */
  public static function has($key)
  {
    switch (self::cacher())
    {
      case 'apc':
        return false === apc_fetch(self::getPrefix().$key) ? false : true;
      case 'xcache':
        return xcache_isset(self::getPrefix().$key);
      case 'eaccelerator':
        return null === eaccelerator_get(self::getPrefix().$key) ? false : true;
    }

    return false;
  }

  /**
   * Clears the cache.
   *
   * @return boolean true if ok, false otherwise
   */
  public static function clear()
  {
    switch (self::cacher())
    {
      case 'apc':
        return apc_clear_cache('user');
      case 'xcache':
        for ($i = 0, $max = xcache_count(XC_TYPE_VAR); $i < $max; $i++)
        {
          if (!xcache_clear_cache(XC_TYPE_VAR, $i))
          {
            return false;
          }
        }
        return true;
      case 'eaccelerator':
        $infos = eaccelerator_list_keys();
        if (is_array($infos))
        {
          foreach ($infos as $info)
          {
            // eaccelerator bug (http://eaccelerator.net/ticket/287)
            $key = 0 === strpos($info['name'], ':') ? substr($info['name'], 1) : $info['name'];
            if (!eaccelerator_rm($key))
            {
              return false;
            }
          }
        }

        return true;
    }

    return false;
  }
}
