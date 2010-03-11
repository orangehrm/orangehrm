<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Cache class that stores cached content in XCache.
 *
 * @package    symfony
 * @subpackage cache
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfXCacheCache.class.php 17858 2009-05-01 21:22:50Z FabianLange $
 */
class sfXCacheCache extends sfCache
{
  /**
   * Initializes this sfCache instance.
   *
   * Available options:
   *
   * * see sfCache for options available for all drivers
   *
   * @see sfCache
   */
  public function initialize($options = array())
  {
    parent::initialize($options);

    if (!function_exists('xcache_set'))
    {
      throw new sfInitializationException('You must have XCache installed and enabled to use sfXCacheCache class.');
    }

    if (!ini_get('xcache.var_size'))
    {
      throw new sfInitializationException('You must set the "xcache.var_size" variable to a value greater than 0 to use sfXCacheCache class.');
    }
  }

 /**
  * @see sfCache
  */
  public function get($key, $default = null)
  {
    
    $set = $this->getBaseValue($key);
    
    if (!is_array($set) || !array_key_exists('data', $set))
    {
      
      return $default;
    }
    
    return $set['data'];
  }

  /**
   * @see sfCache
   */
  public function has($key)
  {
    return xcache_isset($this->getOption('prefix').$key);
  }

  /**
   * @see sfCache
   */
  public function set($key, $data, $lifetime = null)
  {
    $lifetime = $this->getLifetime($lifetime);

    $set = array(
      'timeout' => time() + $lifetime,
      'data'    => $data,
      'ctime'   => time()
    );
    
    return xcache_set($this->getOption('prefix').$key, $set, $lifetime);
  }

  /**
   * @see sfCache
   */
  public function remove($key)
  {
    return xcache_unset($this->getOption('prefix').$key);
  }

  /**
   * @see sfCache
   */
  public function clean($mode = sfCache::ALL)
  {
    if ($mode !== sfCache::ALL)
    {
      return true;
    }

    $this->checkAuth();

    for ($i = 0, $max = xcache_count(XC_TYPE_VAR); $i < $max; $i++)
    {
      if (false === xcache_clear_cache(XC_TYPE_VAR, $i))
      {
        return false;
      }
    }

    return true;
  }

  /**
   * @see sfCache
   */
  public function getLastModified($key)
  {
    $set = $this->getBaseValue($key);
    
    if (!is_array($set) || !array_key_exists('ctime', $set))
    {
      
      return 0;
    }
    
    return $set['ctime'];
  }

  /**
   * @see sfCache
   */
  public function getTimeout($key)
  {
    
    $set = $this->getBaseValue($key);
    
    if (!is_array($set) || !array_key_exists('timeout', $set))
    {
      
      return 0;
    }
    
    return $set['timeout'];
  }
  
  public function getBaseValue($key)
  {
    return xcache_isset($this->getOption('prefix').$key) ? xcache_get($this->getOption('prefix').$key) : null;
  }
  
  /**
   * @see sfCache
   */
  public function removePattern($pattern)
  {
    $this->checkAuth();

    $regexp = self::patternToRegexp($this->getOption('prefix').$pattern);

    for ($i = 0, $max = xcache_count(XC_TYPE_VAR); $i < $max; $i++)
    {
      $infos = xcache_list(XC_TYPE_VAR, $i);
      if (!is_array($infos['cache_list']))
      {
        return;
      }

      foreach ($infos['cache_list'] as $info)
      {
        if (preg_match($regexp, $info['name']))
        {
          xcache_unset($info['name']);
        }
      }
    }
  }

  protected function getCacheInfo($key)
  {
    $this->checkAuth();

    for ($i = 0, $max = xcache_count(XC_TYPE_VAR); $i < $max; $i++)
    {
      $infos = xcache_list(XC_TYPE_VAR, $i);

      if (is_array($infos['cache_list']))
      {
        foreach ($infos['cache_list'] as $info)
        {
          if ($this->getOption('prefix').$key == $info['name'])
          {
            return $info;
          }
        }
      }
    }

    return null;
  }

  protected function checkAuth()
  {
    if (ini_get('xcache.admin.enable_auth'))
    {
      throw new sfConfigurationException('To use all features of the "sfXCacheCache" class, you must set "xcache.admin.enable_auth" to "Off" in your php.ini.');
    }
  }
}
