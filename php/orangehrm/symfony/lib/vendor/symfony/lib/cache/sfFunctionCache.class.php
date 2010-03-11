<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This class can be used to cache the result and output of any PHP callable (function and method calls).
 *
 * @package    symfony
 * @subpackage cache
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfFunctionCache.class.php 17858 2009-05-01 21:22:50Z FabianLange $
 */
class sfFunctionCache
{
  protected $cache = null;

  /**
   * Constructor.
   *
   * @param sfCache $cache An sfCache object instance
   */
  public function __construct($cache)
  {
    if (!is_object($cache))
    {
      $this->cache = new sfFileCache($cache);

      throw new sfException('DEPRECATED: You must now pass a sfCache object when initializing a sfFunctionCache object. Be warned that the call() method signature has also changed.');
    }

    $this->cache = $cache;
  }

  /**
   * Calls a cacheable function or method (or not if there is already a cache for it).
   *
   * Arguments of this method are read with func_get_args. So it doesn't appear in the function definition.
   *
   * The first argument can be any PHP callable:
   *
   * $cache->call('functionName', array($arg1, $arg2));
   * $cache->call(array($object, 'methodName'), array($arg1, $arg2));
   *
   * @param mixed $callable  A PHP callable
   * @param array $arguments An array of arguments to pass to the callable
   *
   * @return mixed The result of the function/method
   */
  public function call($callable, $arguments = array())
  {
    // Generate a cache id
    $key = md5(serialize($callable).serialize($arguments));

    $serialized = $this->cache->get($key);
    if ($serialized !== null)
    {
      $data = unserialize($serialized);
    }
    else
    {
      $data = array();

      if (!is_callable($callable))
      {
        throw new sfException('The first argument to call() must be a valid callable.');
      }

      ob_start();
      ob_implicit_flush(false);

      try
      {
        $data['result'] = call_user_func_array($callable, $arguments);
      }
      catch (Exception $e)
      {
        ob_end_clean();
        throw $e;
      }

      $data['output'] = ob_get_clean();

      $this->cache->set($key, serialize($data));
    }

    echo $data['output'];

    return $data['result'];
  }
}
