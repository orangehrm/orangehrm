<?php

require_once(dirname(__FILE__).'/../vendor/lime/lime.php');

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
 * @version    SVN: $Id: sfTestFunctionalBase.class.php 28641 2010-03-21 10:20:44Z fabien $
 */
abstract class sfTestFunctionalBase
{
  protected
    $testers       = array(),
    $blockTester   = null,
    $currentTester = null,
    $browser       = null;

  protected static
    $test = null;

  /**
   * Initializes the browser tester instance.
   *
   * @param sfBrowserBase $browser A sfBrowserBase instance
   * @param lime_test     $lime    A lime instance
   */
  public function __construct(sfBrowserBase $browser, lime_test $lime = null, $testers = array())
  {
    $this->browser = $browser;

    if (null === self::$test)
    {
      self::$test = null !== $lime ? $lime : new lime_test();
    }

    $this->setTesters(array_merge(array(
      'request'  => 'sfTesterRequest',
      'response' => 'sfTesterResponse',
      'user'     => 'sfTesterUser',
      'mailer'   => 'sfTesterMailer',
    ), $testers));

    // register our shutdown function
    register_shutdown_function(array($this, 'shutdown'));

    // register our error/exception handlers
    set_error_handler(array($this, 'handlePhpError'));
    set_exception_handler(array($this, 'handleException'));
  }

  /**
   * Returns the tester associated with the given name.
   *
   * @param string   $name The tester name
   *
   * @param sfTester A sfTester instance
   */
  public function with($name)
  {
    if (!isset($this->testers[$name]))
    {
      throw new InvalidArgumentException(sprintf('The "%s" tester does not exist.', $name));
    }

    if ($this->blockTester)
    {
      throw new LogicException(sprintf('You cannot nest tester blocks.'));
    }

    $this->currentTester = $this->testers[$name];
    $this->currentTester->initialize();

    return $this->currentTester;
  }

  /**
   * Begins a block of test for the current tester.
   *
   * @return sfTester The current sfTester instance
   */
  public function begin()
  {
    if (!$this->currentTester)
    {
      throw new LogicException(sprintf('You must call with() before beginning a tester block.'));
    }

    return $this->blockTester = $this->currentTester;
  }

  /**
   * End a block of test for the current tester.
   *
   * @return sfTestFunctionalBase
   */
  public function end()
  {
    if (null === $this->blockTester)
    {
      throw new LogicException(sprintf('There is no current tester block to end.'));
    }

    $this->blockTester = null;

    return $this;
  }

  /**
   * Sets the testers.
   *
   * @param array $testers An array of named testers
   */
  public function setTesters($testers)
  {
    foreach ($testers as $name => $tester)
    {
      $this->setTester($name, $tester);
    }
  }

  /**
   * Sets a tester.
   *
   * @param string          $name   The tester name
   * @param sfTester|string $tester A sfTester instance or a tester class name
   */
  public function setTester($name, $tester)
  {
    if (is_string($tester))
    {
      $tester = new $tester($this, self::$test);
    }

    if (!$tester instanceof sfTester)
    {
      throw new InvalidArgumentException(sprintf('The tester "%s" is not of class sfTester.', $name));
    }

    $this->testers[$name] = $tester;
  }

  /**
   * Shutdown function.
   *
   * @return void
   */
  public function shutdown()
  {
    $this->checkCurrentExceptionIsEmpty();
  }

  /**
   * Retrieves the lime_test instance.
   *
   * @return lime_test The lime_test instance
   */
  public function test()
  {
    return self::$test;
  }

  /**
   * Gets a uri.
   *
   * @param string $uri         The URI to fetch
   * @param array  $parameters  The Request parameters
   * @param bool   $changeStack  Change the browser history stack?
   *
   * @return sfTestFunctionalBase
   */
  public function get($uri, $parameters = array(), $changeStack = true)
  {
    return $this->call($uri, 'get', $parameters, $changeStack);
  }

  /**
   * Retrieves and checks an action.
   *
   * @param  string $module  Module name
   * @param  string $action  Action name
   * @param  string $url     Url
   * @param  string $code    The expected return status code
   *
   * @return sfTestFunctionalBase The current sfTestFunctionalBase instance
   */
  public function getAndCheck($module, $action, $url = null, $code = 200)
  {
    return $this->
      get(null !== $url ? $url : sprintf('/%s/%s', $module, $action))->
      with('request')->begin()->
        isParameter('module', $module)->
        isParameter('action', $action)->
      end()->
      with('response')->isStatusCode($code)
    ;
  }

  /**
   * Posts a uri.
   *
   * @param string $uri         The URI to fetch
   * @param array  $parameters  The Request parameters
   * @param bool   $changeStack  Change the browser history stack?
   *
   * @return sfTestFunctionalBase
   */
  public function post($uri, $parameters = array(), $changeStack = true)
  {
    return $this->call($uri, 'post', $parameters, $changeStack);
  }

  /**
   * Calls a request.
   *
   * @param  string $uri          URI to be invoked
   * @param  string $method       HTTP method used
   * @param  array  $parameters   Additional parameters
   * @param  bool   $changeStack  If set to false ActionStack is not changed
   *
   * @return sfTestFunctionalBase The current sfTestFunctionalBase instance
   */
  public function call($uri, $method = 'get', $parameters = array(), $changeStack = true)
  {
    $this->checkCurrentExceptionIsEmpty();

    $uri = $this->browser->fixUri($uri);

    $this->test()->comment(sprintf('%s %s', strtolower($method), $uri));

    foreach ($this->testers as $tester)
    {
      $tester->prepare();
    }

    $this->browser->call($uri, $method, $parameters, $changeStack);

    return $this;
  }

  /**
   * Simulates deselecting a checkbox or radiobutton.
   *
   * @param string  $name       The checkbox or radiobutton id, name or text
   *
   * @return sfTestFunctionalBase
   */
  public function deselect($name)
  {
    $this->browser->doSelect($name, false);

    return $this;
  }

  /**
   * Simulates selecting a checkbox or radiobutton.
   *
   * @param string  $name       The checkbox or radiobutton id, name or text
   *
   * @return sfTestFunctionalBase
   */
  public function select($name)
  {
    $this->browser->doSelect($name, true);

    return $this;
  }

  /**
   * Simulates a click on a link or button.
   *
   * @param string  $name       The link or button text
   * @param array   $arguments  The arguments to pass to the link
   * @param array   $options    An array of options
   *
   * @return sfTestFunctionalBase
   */
  public function click($name, $arguments = array(), $options = array())
  {
    if ($name instanceof DOMElement)
    {
      list($uri, $method, $parameters) = $this->doClickElement($name, $arguments, $options);
    }
    else
    {
      try
      {
        list($uri, $method, $parameters) = $this->doClick($name, $arguments, $options);
      }
      catch (InvalidArgumentException $e)
      {
        list($uri, $method, $parameters) = $this->doClickCssSelector($name, $arguments, $options);
      }
    }

    return $this->call($uri, $method, $parameters);
  }

  /**
   * Simulates the browser back button.
   *
   * @return sfTestFunctionalBase The current sfTestFunctionalBase instance
   */
  public function back()
  {
    $this->test()->comment('back');

    $this->browser->back();

    return $this;
  }

  /**
   * Simulates the browser forward button.
   *
   * @return sfTestFunctionalBase The current sfTestFunctionalBase instance
   */
  public function forward()
  {
    $this->test()->comment('forward');

    $this->browser->forward();

    return $this;
  }

  /**
   * Outputs an information message.
   *
   * @param string $message A message
   *
   * @return sfTestFunctionalBase The current sfTestFunctionalBase instance
   */
  public function info($message)
  {
    $this->test()->info($message);

    return $this;
  }

  /**
   * Checks that the current response contains a given text.
   *
   * @param  string $uri   Uniform resource identifier
   * @param  string $text  Text in the response
   *
   * @return sfTestFunctionalBase The current sfTestFunctionalBase instance
   */
  public function check($uri, $text = null)
  {
    $this->get($uri)->with('response')->isStatusCode();

    if ($text !== null)
    {
      $this->with('response')->contains($text);
    }

    return $this;
  }

  /**
   * Tests if an exception is thrown by the latest request.
   *
   * @param  string $class    Class name
   * @param  string $message  Message name
   *
   * @return sfTestFunctionalBase The current sfTestFunctionalBase instance
   */
  public function throwsException($class = null, $message = null)
  {
    $e = $this->browser->getCurrentException();

    if (null === $e)
    {
      $this->test()->fail('response returns an exception');
    }
    else
    {
      if (null !== $class)
      {
        $this->test()->ok($e instanceof $class, sprintf('response returns an exception of class "%s"', $class));
      }

      if (null !== $message && preg_match('/^(!)?([^a-zA-Z0-9\\\\]).+?\\2[ims]?$/', $message, $match))
      {
        if ($match[1] == '!')
        {
          $this->test()->unlike($e->getMessage(), substr($message, 1), sprintf('response exception message does not match regex "%s"', $message));
        }
        else
        {
          $this->test()->like($e->getMessage(), $message, sprintf('response exception message matches regex "%s"', $message));
        }
      }
      else if (null !== $message)
      {
        $this->test()->is($e->getMessage(), $message, sprintf('response exception message is "%s"', $message));
      }
    }

    $this->resetCurrentException();

    return $this;
  }

  /**
   * Triggers a test failure if an uncaught exception is present.
   *
   * @return  bool
   */
  public function checkCurrentExceptionIsEmpty()
  {
    if (false === ($empty = $this->browser->checkCurrentExceptionIsEmpty()))
    {
      $this->test()->fail(sprintf('last request threw an uncaught exception "%s: %s"', get_class($this->browser->getCurrentException()), $this->browser->getCurrentException()->getMessage()));
    }

    return $empty;
  }

  public function __call($method, $arguments)
  {
    $retval = call_user_func_array(array($this->browser, $method), $arguments);

    // fix the fluent interface
    return $retval === $this->browser ? $this : $retval;
  }

  /**
   * Error handler for the current test browser instance.
   *
   * @param mixed  $errno    Error number
   * @param string $errstr   Error message
   * @param string $errfile  Error file
   * @param mixed  $errline  Error line
   */
  static public function handlePhpError($errno, $errstr, $errfile, $errline)
  {
    if (($errno & error_reporting()) == 0)
    {
      return false;
    }

    $msg = sprintf('PHP sent a "%%s" error at %s line %s (%s)', $errfile, $errline, $errstr);
    switch ($errno)
    {
      case E_WARNING:
        $msg = sprintf($msg, 'warning');
        throw new RuntimeException($msg);
        break;
      case E_NOTICE:
        $msg = sprintf($msg, 'notice');
        throw new RuntimeException($msg);
        break;
      case E_STRICT:
        $msg = sprintf($msg, 'strict');
        throw new RuntimeException($msg);
        break;
      case E_RECOVERABLE_ERROR:
        $msg = sprintf($msg, 'catchable');
        throw new RuntimeException($msg);
        break;
    }

    return false;
  }

  /**
   * Exception handler for the current test browser instance.
   *
   * @param Exception $exception The exception
   */
  function handleException(Exception $exception)
  {
    $this->test()->error(sprintf('%s: %s', get_class($exception), $exception->getMessage()));

    $traceData = $exception->getTrace();
    array_unshift($traceData, array(
      'function' => '',
      'file'     => $exception->getFile() != null ? $exception->getFile() : 'n/a',
      'line'     => $exception->getLine() != null ? $exception->getLine() : 'n/a',
      'args'     => array(),
    ));

    $traces = array();
    $lineFormat = '  at %s%s%s() in %s line %s';
    for ($i = 0, $count = count($traceData); $i < $count; $i++)
    {
      $line = isset($traceData[$i]['line']) ? $traceData[$i]['line'] : 'n/a';
      $file = isset($traceData[$i]['file']) ? $traceData[$i]['file'] : 'n/a';
      $args = isset($traceData[$i]['args']) ? $traceData[$i]['args'] : array();
      $this->test()->error(sprintf($lineFormat,
        (isset($traceData[$i]['class']) ? $traceData[$i]['class'] : ''),
        (isset($traceData[$i]['type']) ? $traceData[$i]['type'] : ''),
        $traceData[$i]['function'],
        $file,
        $line
      ));
    }

    $this->test()->fail('An uncaught exception has been thrown.');
  }
}
