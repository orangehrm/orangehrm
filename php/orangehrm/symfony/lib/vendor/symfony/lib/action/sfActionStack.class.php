<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) 2004-2006 Sean Kerr <sean@code-box.org>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfActionStack keeps a list of all requested actions and provides accessor
 * methods for retrieving individual entries.
 *
 * @package    symfony
 * @subpackage action
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Sean Kerr <sean@code-box.org>
 * @version    SVN: $Id: sfActionStack.class.php 17858 2009-05-01 21:22:50Z FabianLange $
 */
class sfActionStack
{
  protected
    $stack = array();

  /**
   * Adds an entry to the action stack.
   *
   * @param string   $moduleName     A module name
   * @param string   $actionName     An action name
   * @param sfAction $actionInstance An sfAction implementation instance
   *
   * @return sfActionStackEntry sfActionStackEntry instance
   */
  public function addEntry($moduleName, $actionName, $actionInstance)
  {
    // create our action stack entry and add it to our stack
    $actionEntry = new sfActionStackEntry($moduleName, $actionName, $actionInstance);

    $this->stack[] = $actionEntry;

    return $actionEntry;
  }

  /**
   * Retrieves the entry at a specific index.
   *
   * @param int $index An entry index
   *
   * @return sfActionStackEntry An action stack entry implementation.
   */
  public function getEntry($index)
  {
    $retval = null;

    if ($index > -1 && $index < count($this->stack))
    {
      $retval = $this->stack[$index];
    }

    return $retval;
  }

  /**
   * Removes the entry at a specific index.
   *
   * @return sfActionStackEntry An action stack entry implementation.
   */
  public function popEntry()
  {
    return array_pop($this->stack);
  }

  /**
   * Retrieves the first entry.
   *
   * @return mixed An action stack entry implementation or null if there is no sfAction instance in the stack
   */
  public function getFirstEntry()
  {
    $retval = null;

    if (isset($this->stack[0]))
    {
      $retval = $this->stack[0];
    }

    return $retval;
  }

  /**
   * Retrieves the last entry.
   *
   * @return mixed An action stack entry implementation or null if there is no sfAction instance in the stack
   */
  public function getLastEntry()
  {
    $count  = count($this->stack);
    $retval = null;

    if (isset($this->stack[0]))
    {
      $retval = $this->stack[$count - 1];
    }

    return $retval;
  }

  /**
   * Retrieves the size of this stack.
   *
   * @return int The size of this stack.
   */
  public function getSize()
  {
    return count($this->stack);
  }
}
