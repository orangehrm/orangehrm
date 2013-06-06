<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfTestFunctional tests an application by using a browser simulator.
 *
 * @package    symfony
 * @subpackage test
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfTestFunctional.class.php 23937 2009-11-14 17:43:12Z fabien $
 */
class sfTestFunctional extends sfTestFunctionalBase
{
  /**
   * Initializes the browser tester instance.
   *
   * @param sfBrowserBase $browser A sfBrowserBase instance
   * @param lime_test     $lime    A lime instance
   */
  public function __construct(sfBrowserBase $browser, lime_test $lime = null, $testers = array())
  {
    $testers = array_merge(array(
      'view_cache' => 'sfTesterViewCache',
      'form'       => 'sfTesterForm',
    ), $testers);

    parent::__construct($browser, $lime, $testers);
  }

  /**
   * Checks that the request is forwarded to a given module/action.
   *
   * @param  string $moduleName  The module name
   * @param  string $actionName  The action name
   * @param  mixed  $position    The position in the action stack (default to the last entry)
   *
   * @return sfTestFunctional The current sfTestFunctional instance
   */
  public function isForwardedTo($moduleName, $actionName, $position = 'last')
  {
    $actionStack = $this->browser->getContext()->getActionStack();

    switch ($position)
    {
      case 'first':
        $entry = $actionStack->getFirstEntry();
        break;
      case 'last':
        $entry = $actionStack->getLastEntry();
        break;
      default:
        $entry = $actionStack->getEntry($position);
    }

    $this->test()->is($entry->getModuleName(), $moduleName, sprintf('request is forwarded to the "%s" module (%s)', $moduleName, $position));
    $this->test()->is($entry->getActionName(), $actionName, sprintf('request is forwarded to the "%s" action (%s)', $actionName, $position));

    return $this;
  }
}
