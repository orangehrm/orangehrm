<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfVarLogger logs messages within its instance for later use.
 *
 * @package    symfony
 * @subpackage log
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfVarLogger.class.php 26989 2010-01-21 12:43:21Z FabianLange $
 */
class sfVarLogger extends sfLogger
{
  protected
    $logs          = array(),
    $xdebugLogging = false;

  /**
   * Initializes this logger.
   *
   * Available options:
   *
   * - xdebug_logging: Whether to add xdebug trace to the logs (false by default).
   *
   * @param  sfEventDispatcher $dispatcher  A sfEventDispatcher instance
   * @param  array             $options     An array of options.
   *
   * @return Boolean           true, if initialization completes successfully, otherwise false.
   */
  public function initialize(sfEventDispatcher $dispatcher, $options = array())
  {
    $this->xdebugLogging = isset($options['xdebug_logging']) ? $options['xdebug_logging'] : false;

    // disable xdebug when an HTTP debug session exists (crashes Apache, see #2438)
    if (isset($_GET['XDEBUG_SESSION_START']) || isset($_COOKIE['XDEBUG_SESSION']))
    {
      $this->xdebugLogging = false;
    }

    return parent::initialize($dispatcher, $options);
  }

  /**
   * Gets the logs.
   *
   * Each log entry has the following attributes:
   *
   *  * priority
   *  * time   
   *  * message
   *  * type
   *  * debugStack
   *
   * @return array An array of logs
   */
  public function getLogs()
  {
    return $this->logs;
  }

  /**
   * Returns all the types in the logs.
   *
   * @return array An array of types
   */
  public function getTypes()
  {
    $types = array();
    foreach ($this->logs as $log)
    {
      if (!in_array($log['type'], $types))
      {
        $types[] = $log['type'];
      }
    }

    sort($types);

    return $types;
  }

  /**
   * Returns all the priorities in the logs.
   *
   * @return array An array of priorities
   */
  public function getPriorities()
  {
    $priorities = array();
    foreach ($this->logs as $log)
    {
      if (!in_array($log['priority'], $priorities))
      {
        $priorities[] = $log['priority'];
      }
    }

    sort($priorities);

    return $priorities;
  }

  /**
   * Returns the highest priority in the logs.
   *
   * @return integer The highest priority
   */
  public function getHighestPriority()
  {
    $priority = 1000;
    foreach ($this->logs as $log)
    {
      if ($log['priority'] < $priority)
      {
        $priority = $log['priority'];
      }
    }

    return $priority;
  }

  /**
   * Logs a message.
   *
   * @param string $message   Message
   * @param string $priority  Message priority
   */
  protected function doLog($message, $priority)
  {
    // get log type in {}
    $type = 'sfOther';
    if (preg_match('/^\s*{([^}]+)}\s*(.+?)$/s', $message, $matches))
    {
      $type    = $matches[1];
      $message = $matches[2];
    }

    $this->logs[] = array(
      'priority'        => $priority,
      'priority_name'   => $this->getPriorityName($priority),
      'time'            => time(),
      'message'         => $message,
      'type'            => $type,
      'debug_backtrace' => $this->getDebugBacktrace(),
    );
  }

  /**
   * Returns the debug stack.
   *
   * @return array
   * 
   * @see debug_backtrace()
   */
  protected function getDebugBacktrace()
  {
    // if we have xdebug and dev has not disabled the feature, add some stack information
    if (!$this->xdebugLogging || !function_exists('debug_backtrace'))
    {
      return array();
    }

    $traces = debug_backtrace();

    // remove sfLogger and sfEventDispatcher from the top of the trace
    foreach ($traces as $i => $trace)
    {
      $class = isset($trace['class']) ? $trace['class'] : substr($file = basename($trace['file']), 0, strpos($file, '.'));

      if (
        !class_exists($class)
        ||
        (!in_array($class, array('sfLogger', 'sfEventDispatcher')) && !is_subclass_of($class, 'sfLogger') && !is_subclass_of($class, 'sfEventDispatcher'))
      )
      {
        $traces = array_slice($traces, $i);
        break;
      }
    }

    return $traces;
  }
}
