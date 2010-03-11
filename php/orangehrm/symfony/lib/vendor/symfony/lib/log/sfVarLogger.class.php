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
 * @version    SVN: $Id: sfVarLogger.class.php 14173 2008-12-18 12:49:57Z Kris.Wallsmith $
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
      if (!in_array($log['priority'], $types))
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
      'priority'      => $priority,
      'priority_name' => $this->getPriorityName($priority),
      'time'          => time(),
      'message'       => $message,
      'type'          => $type,
      'debug_stack'   => $this->getXDebugStack(),
    );
  }

  /**
   * Returns the xdebug stack.
   *
   * @return array The xdebug stack as an array
   */
  protected function getXDebugStack()
  {
    // if we have xdebug and dev has not disabled the feature, add some stack information
    if (!$this->xdebugLogging || !function_exists('xdebug_get_function_stack'))
    {
      return array();
    }

    $debugStack = array();
    foreach (xdebug_get_function_stack() as $i => $stack)
    {
      if (
        (isset($stack['function']) && !in_array($stack['function'], array('emerg', 'alert', 'crit', 'err', 'warning', 'notice', 'info', 'debug', 'log')))
        || !isset($stack['function'])
      )
      {
        $tmp = '';
        if (isset($stack['function']))
        {
          $tmp .= sprintf('in "%s" ', $stack['function']);
        }
        $tmp .= sprintf('from "%s" line %s', $stack['file'], $stack['line']);
        $debugStack[] = $tmp;
      }
    }

    return $debugStack;
  }
}
