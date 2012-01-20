<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfTesterRequest implements tests for the symfony request object.
 *
 * @package    symfony
 * @subpackage test
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfTesterRequest.class.php 27845 2010-02-10 19:46:03Z Kris.Wallsmith $
 */
class sfTesterRequest extends sfTester
{
  protected $request;

  /**
   * Prepares the tester.
   */
  public function prepare()
  {
  }

  /**
   * Initializes the tester.
   */
  public function initialize()
  {
    $this->request = $this->browser->getRequest();
  }

  /**
   * Tests whether or not a given key and value exists in the request.
   *
   * @param string $key
   * @param string $value
   *
   * @return sfTestFunctionalBase|sfTester
   */
  public function isParameter($key, $value)
  {
    $this->tester->is($this->request->getParameter($key), $value, sprintf('request parameter "%s" is "%s"', $key, $value));

    return $this->getObjectToReturn();
  }

  /**
   * Tests for the request is in the given format.
   *
   * @param  string $format  The request format
   *
   * @return sfTestFunctionalBase|sfTester
   */
  public function isFormat($format)
  {
    $this->tester->is($this->request->getRequestFormat(), $format, sprintf('request format is "%s"', $format));

    return $this->getObjectToReturn();
  }

  /**
   * Tests if the current HTTP method matches the given one
   *
   * @param  string  $method  The HTTP method name
   *
   * @return sfTestFunctionalBase|sfTester
   */
  public function isMethod($method)
  {
    $this->tester->ok($this->request->isMethod($method), sprintf('request method is "%s"', strtoupper($method)));

    return $this->getObjectToReturn();
  }

  /**
   * Checks if a cookie exists.
   *
   * @param string  $name   The cookie name
   * @param Boolean $exists Whether the cookie must exist or not
   *
   * @return sfTestFunctionalBase|sfTester
   */
  public function hasCookie($name, $exists = true)
  {
    if (!array_key_exists($name, $_COOKIE))
    {
      if ($exists)
      {
        $this->tester->fail(sprintf('cookie "%s" exists.', $name));
      }
      else
      {
        $this->tester->pass(sprintf('cookie "%s" does not exist.', $name));
      }

      return $this->getObjectToReturn();
    }

    if ($exists)
    {
      $this->tester->pass(sprintf('cookie "%s" exists.', $name));
    }
    else
    {
      $this->tester->fail(sprintf('cookie "%s" does not exist.', $name));
    }

    return $this->getObjectToReturn();
  }

  /**
   * Checks the value of a cookie.
   *
   * @param string $name   The cookie name
   * @param mixed  $value  The expected value
   *
   * @return sfTestFunctionalBase|sfTester
   */
  public function isCookie($name, $value)
  {
    if (!array_key_exists($name, $_COOKIE))
    {
      $this->tester->fail(sprintf('cookie "%s" does not exist.', $name));

      return $this->getObjectToReturn();
    }

    if (preg_match('/^(!)?([^a-zA-Z0-9\\\\]).+?\\2[ims]?$/', $value, $match))
    {
      if ($match[1] == '!')
      {
        $this->tester->unlike($_COOKIE[$name], substr($value, 1), sprintf('cookie "%s" content does not match regex "%s"', $name, $value));
      }
      else
      {
        $this->tester->like($_COOKIE[$name], $value, sprintf('cookie "%s" content matches regex "%s"', $name, $value));
      }
    }
    else
    {
      $this->tester->is($_COOKIE[$name], $value, sprintf('cookie "%s" content is ok', $name));
    }

    return $this->getObjectToReturn();
  }
}
