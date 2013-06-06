<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Cache class that does nothing.
 *
 * @package    symfony
 * @subpackage cache
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfNoCache.class.php 10970 2008-08-19 19:02:38Z fabien $
 */
class sfNoCache extends sfCache
{
  /**
   * @see sfCache
   */
  public function get($key, $default = null)
  {
    return $default;
  }

  /**
   * @see sfCache
   */
  public function has($key)
  {
    return false;
  }

  /**
   * @see sfCache
   */
  public function set($key, $data, $lifetime = null)
  {
    return true;
  }

  /**
   * @see sfCache
   */
  public function remove($key)
  {
    return true;
  }

  /**
   * @see sfCache
   */
  public function removePattern($pattern)
  {
    return true;
  }

  /**
   * @see sfCache
   */
  public function clean($mode = self::ALL)
  {
    return true;
  }

  /**
   * @see sfCache
   */
  public function getLastModified($key)
  {
    return 0;
  }

  /**
   * @see sfCache
   */
  public function getTimeout($key)
  {
    return 0;
  }
}
