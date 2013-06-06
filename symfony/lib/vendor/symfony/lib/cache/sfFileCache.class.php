<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Cache class that stores content in files.
 *
 * @package    symfony
 * @subpackage cache
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfFileCache.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class sfFileCache extends sfCache
{
  const READ_DATA = 1;
  const READ_TIMEOUT = 2;
  const READ_LAST_MODIFIED = 4;

  const EXTENSION = '.cache';

 /**
  * Initializes this sfCache instance.
  *
  * Available options:
  *
  * * cache_dir: The directory where to put cache files
  *
  * * see sfCache for options available for all drivers
  *
  * @see sfCache
  */
  public function initialize($options = array())
  {
    parent::initialize($options);

    if (!$this->getOption('cache_dir'))
    {
      throw new sfInitializationException('You must pass a "cache_dir" option to initialize a sfFileCache object.');
    }

    $this->setcache_dir($this->getOption('cache_dir'));
  }

  /**
   * @see sfCache
   */
  public function get($key, $default = null)
  {
    $file_path = $this->getFilePath($key);
    if (!file_exists($file_path))
    {
      return $default;
    }

    $data = $this->read($file_path, self::READ_DATA);

    if ($data[self::READ_DATA] === null)
    {
      return $default;
    }

    return $data[self::READ_DATA];
  }

  /**
   * @see sfCache
   */
  public function has($key)
  {
    $path = $this->getFilePath($key);
    return file_exists($path) && $this->isValid($path);
  }

  /**
   * @see sfCache
   */
  public function set($key, $data, $lifetime = null)
  {
    if ($this->getOption('automatic_cleaning_factor') > 0 && rand(1, $this->getOption('automatic_cleaning_factor')) == 1)
    {
      $this->clean(sfCache::OLD);
    }

    return $this->write($this->getFilePath($key), $data, time() + $this->getLifetime($lifetime));
  }

  /**
   * @see sfCache
   */
  public function remove($key)
  {
    return @unlink($this->getFilePath($key));
  }

  /**
   * @see sfCache
   */
  public function removePattern($pattern)
  {
    if (false !== strpos($pattern, '**'))
    {
      $pattern = str_replace(sfCache::SEPARATOR, DIRECTORY_SEPARATOR, $pattern).self::EXTENSION;

      $regexp = self::patternToRegexp($pattern);
      $paths = array();
      foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->getOption('cache_dir'))) as $path)
      {
        if (preg_match($regexp, str_replace($this->getOption('cache_dir').DIRECTORY_SEPARATOR, '', $path)))
        {
          $paths[] = $path;
        }
      }
    }
    else
    {
      $paths = glob($this->getOption('cache_dir').DIRECTORY_SEPARATOR.str_replace(sfCache::SEPARATOR, DIRECTORY_SEPARATOR, $pattern).self::EXTENSION);
    }

    foreach ($paths as $path)
    {
      if (is_dir($path))
      {
        sfToolkit::clearDirectory($path);
      }
      else
      {
        @unlink($path);
      }
    }
  }

  /**
   * @see sfCache
   */
  public function clean($mode = sfCache::ALL)
  {
    if (!is_dir($this->getOption('cache_dir')))
    {
      return true;
    }

    $result = true;
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->getOption('cache_dir'))) as $file)
    {
      if (sfCache::ALL == $mode || !$this->isValid($file))
      {
        $result = @unlink($file) && $result;
      }
    }

    return $result;
  }

  /**
   * @see sfCache
   */
  public function getTimeout($key)
  {
    $path = $this->getFilePath($key);

    if (!file_exists($path))
    {
      return 0;
    }

    $data = $this->read($path, self::READ_TIMEOUT);

    return $data[self::READ_TIMEOUT] < time() ? 0 : $data[self::READ_TIMEOUT];
  }

  /**
   * @see sfCache
   */
  public function getLastModified($key)
  {
    $path = $this->getFilePath($key);

    if (!file_exists($path))
    {
      return 0;
    }
    
    $data = $this->read($path, self::READ_TIMEOUT | self::READ_LAST_MODIFIED);

    if ($data[self::READ_TIMEOUT] < time())
    {
      return 0;
    }
    return $data[self::READ_LAST_MODIFIED];
  }

  protected function isValid($path)
  {
    $data = $this->read($path, self::READ_TIMEOUT);
    return time() < $data[self::READ_TIMEOUT];
  }

 /**
  * Converts a cache key to a full path.
  *
  * @param string $key The cache key
  *
  * @return string The full path to the cache file
  */
  protected function getFilePath($key)
  {
    return $this->getOption('cache_dir').DIRECTORY_SEPARATOR.str_replace(sfCache::SEPARATOR, DIRECTORY_SEPARATOR, $key).self::EXTENSION;
  }

 /**
  * Reads the cache file and returns the content.
  *
  * @param string $path The file path
  * @param mixed  $type The type of data you want to be returned
  *                     sfFileCache::READ_DATA: The cache content
  *                     sfFileCache::READ_TIMEOUT: The timeout
  *                     sfFileCache::READ_LAST_MODIFIED: The last modification timestamp
  *
  * @return array the (meta)data of the cache file. E.g. $data[sfFileCache::READ_DATA]
  *
  * @throws sfCacheException
  */
  protected function read($path, $type = self::READ_DATA)
  {
    if (!$fp = @fopen($path, 'rb'))
    {
      throw new sfCacheException(sprintf('Unable to read cache file "%s".', $path));
    }

    @flock($fp, LOCK_SH);
    $data[self::READ_TIMEOUT] = intval(@stream_get_contents($fp, 12, 0));
    if ($type != self::READ_TIMEOUT && time() < $data[self::READ_TIMEOUT])
    {
      if ($type & self::READ_LAST_MODIFIED)
      {
        $data[self::READ_LAST_MODIFIED] = intval(@stream_get_contents($fp, 12, 12));
      }
      if ($type & self::READ_DATA)
      {
        fseek($fp, 0, SEEK_END);
        $length = ftell($fp) - 24;
        fseek($fp, 24);
        $data[self::READ_DATA] = @fread($fp, $length);
      }
    }
    else
    {
      $data[self::READ_LAST_MODIFIED] = null;
      $data[self::READ_DATA] = null;
    }
    @flock($fp, LOCK_UN);
    @fclose($fp);

    return $data;
  }

 /**
  * Writes the given data in the cache file.
  *
  * @param string  $path    The file path
  * @param string  $data    The data to put in cache
  * @param integer $timeout The timeout timestamp
  *
  * @return boolean true if ok, otherwise false
  *
  * @throws sfCacheException
  */
  protected function write($path, $data, $timeout)
  {
    $current_umask = umask();
    umask(0000);

    if (!is_dir(dirname($path)))
    {
      // create directory structure if needed
      mkdir(dirname($path), 0777, true);
    }

    $tmpFile = tempnam(dirname($path), basename($path));

    if (!$fp = @fopen($tmpFile, 'wb'))
    {
       throw new sfCacheException(sprintf('Unable to write cache file "%s".', $tmpFile));
    }

    @fwrite($fp, str_pad($timeout, 12, 0, STR_PAD_LEFT));
    @fwrite($fp, str_pad(time(), 12, 0, STR_PAD_LEFT));
    @fwrite($fp, $data);
    @fclose($fp);

    // Hack from Agavi (http://trac.agavi.org/changeset/3979)
    // With php < 5.2.6 on win32, renaming to an already existing file doesn't work, but copy does,
    // so we simply assume that when rename() fails that we are on win32 and try to use copy()
    if (!@rename($tmpFile, $path))
    {
      if (copy($tmpFile, $path))
      {
        unlink($tmpFile);
      }
    }

    chmod($path, 0666);
    umask($current_umask);

    return true;
  }

  /**
   * Sets the cache root directory.
   *
   * @param string $cache_dir The directory where to put the cache files
   */
  protected function setcache_dir($cache_dir)
  {
    // remove last DIRECTORY_SEPARATOR
    if (DIRECTORY_SEPARATOR == substr($cache_dir, -1))
    {
      $cache_dir = substr($cache_dir, 0, -1);
    }

    // create cache dir if needed
    if (!is_dir($cache_dir))
    {
      $current_umask = umask(0000);
      @mkdir($cache_dir, 0777, true);
      umask($current_umask);
    }
  }
}
