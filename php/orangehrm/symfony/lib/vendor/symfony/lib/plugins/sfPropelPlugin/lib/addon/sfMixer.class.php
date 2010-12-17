<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfMixer implements mixins and hooks.
 *
 * @package    symfony
 * @subpackage propel
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfMixer.class.php 11108 2008-08-25 11:00:07Z Kris.Wallsmith $
 */
class sfMixer
{
  static protected
    $mixins          = array(),
    $mixinParameters = array(),
    $mixinInstances  = array();

  static public function register($name, $callable)
  {
    $lazy = false;

    if (is_array($callable))
    {
      $mixinClass = $callable[0];
      $mixinMethod = $callable[1];
      if (!is_object($mixinClass))
      {
        $rc = new ReflectionClass($mixinClass);
        $rm = $rc->getMethod($mixinMethod);
        if (!$rm->isStatic())
        {
          $lazy = true;
        }
      }
    }
    else
    {
      $mixinMethod = $callable;
    }

    $tmp = explode(':', $name);
    $class = $tmp[0];

    // do we have a method name
    if (isset($tmp[1]))
    {
      $method = $tmp[1];

      // do we have a hook name
      if (isset($tmp[2]))
      {
        $hook = $tmp[2];
      }
      else
      {
        $hook = $method;
        $name .= ':'.$hook;
      }
    }
    else
    {
      // this will be called with __call
      $method = $mixinMethod;
      $name = $class.':'.$method;
      $hook = '';
    }

    // we cannot register 2 new methods with the same name
    if (!$hook && isset(self::$mixins[$name]))
    {
      throw new Exception(sprintf('The class "%s" has already a mixin for method "%s".', $class, $mixinMethod));
    }

    // register mixin
    if (!isset(self::$mixins[$name]))
    {
      self::$mixins[$name] = array();
    }

    if (!isset(self::$mixinParameters[$name]))
    {
      self::$mixinParameters[$name] = array();
    }

    self::$mixins[$name][] = $callable;
    self::$mixinParameters[$name][] = array(
      'lazy'     => $lazy,
      'class'    => $class,
      'method'   => $method,
      'hook'     => $hook,
    );
  }

  static public function getMixinInstance($name)
  {
    if (!isset(self::$mixins[$name]))
    {
      return;
    }

    foreach (self::$mixins[$name] as $i => $mixin)
    {
      if (!self::$mixinParameters[$name][$i]['lazy'])
      {
        continue;
      }

      $class = $mixin[0];
      if (!isset(self::$mixinInstances[$class]))
      {
        self::$mixinInstances[$class] = new $class();
        if (method_exists(self::$mixinInstances[$class], 'initialize'))
        {
          self::$mixinInstances[$class]->initialize();
        }
      }

      self::$mixinParameters[$name][$i]['lazy'] = false;
      self::$mixins[$name][$i][0] = self::$mixinInstances[$class];
    }
  }

  static public function getCallables($name)
  {
    self::getMixinInstance($name);

    return isset(self::$mixins[$name]) ? self::$mixins[$name] : array();
  }

  static public function getCallable($name)
  {
    self::getMixinInstance($name);

    return isset(self::$mixins[$name]) ? self::$mixins[$name][0] : null;
  }

  static public function callMixins($hookName = null, $moreParams = array())
  {
    $traces = debug_backtrace();
    $function = $traces[1]['function'];
    $parameters = $traces[1]['args'];
    $class = $traces[1]['class'];
    $type = $traces[1]['type'];
    if ('__call' == $function)
    {
      $method     = $parameters[0];
      $parameters = $parameters[1];
    }
    else
    {
      $method = $function;
    }

    if ('->' == $type)
    {
      array_unshift($parameters, $traces[1]['object']);
    }
    else
    {
      array_unshift($parameters, $class);
    }

    // add more parameters
    $parameters = array_merge($parameters, (array) $moreParams);

    if ('__call' == $function)
    {
      if ($callable = self::getCallable($class.':'.$method))
      {
        return call_user_func_array($callable, $parameters);
      }
      else
      {
        throw new Exception(sprintf('Call to undefined method %s::%s.', $class, $method));
      }
    }
    else
    {
      $hookName = $hookName ? $hookName : $method;
      foreach (self::getCallables($class.':'.$method.':'.$hookName) as $callable)
      {
        call_user_func_array($callable, $parameters);
      }
    }
  }
}
