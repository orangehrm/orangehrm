<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfTester is the base class for all tester classes.
 *
 * @package    symfony
 * @subpackage test
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfTester.class.php 13691 2008-12-03 22:17:01Z Kris.Wallsmith $
 */
abstract class sfTester
{
  protected
    $inABlock = false,
    $browser  = null,
    $tester   = null;

  /**
   * Constructor.
   *
   * @param sfTestFunctionalBase $browser A browser
   * @param lime_test            $tester  A tester object
   */
  public function __construct(sfTestFunctionalBase $browser, $tester)
  {
    $this->browser = $browser;
    $this->tester  = $tester;
  }

  /**
   * Prepares the tester.
   */
  abstract public function prepare();

  /**
   * Initializes the tester.
   */
  abstract public function initialize();

  /**
   * Begins a block.
   *
   * @return sfTester This sfTester instance
   */
  public function begin()
  {
    $this->inABlock = true;

    return $this->browser->begin();
  }

  /**
   * Ends a block.
   *
   * @param sfTestFunctionalBase
   */
  public function end()
  {
    $this->inABlock = false;

    return $this->browser->end();
  }

  /**
   * Returns the object that each test method must return.
   *
   * @return sfTestFunctionalBase|sfTester
   */
  public function getObjectToReturn()
  {
    return $this->inABlock ? $this : $this->browser;
  }

  public function __call($method, $arguments)
  {
    call_user_func_array(array($this->browser, $method), $arguments);

    return $this->getObjectToReturn();
  }
}
