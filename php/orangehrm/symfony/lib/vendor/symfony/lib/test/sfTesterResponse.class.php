<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfTesterResponse implements tests for the symfony response object.
 *
 * @package    symfony
 * @subpackage test
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfTesterResponse.class.php 12517 2008-11-01 07:10:55Z fabien $
 */
class sfTesterResponse extends sfTester
{
  protected
    $response       = null,
    $dom            = null,
    $domCssSelector = null;

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
    $this->response = $this->browser->getResponse();

    $this->dom = null;
    $this->domCssSelector = null;
    if (preg_match('/(x|ht)ml/i', $this->response->getContentType(), $matches))
    {
      $this->dom = new DomDocument('1.0', $this->response->getCharset());
      $this->dom->validateOnParse = true;
      if ('x' == $matches[1])
      {
        @$this->dom->loadXML($this->response->getContent());
      }
      else
      {
        @$this->dom->loadHTML($this->response->getContent());
      }
      $this->domCssSelector = new sfDomCssSelector($this->dom);
    }
  }

  /**
   * Tests that the response matches a given CSS selector.
   *
   * @param  string $selector  The response selector or a sfDomCssSelector object
   * @param  mixed  $value     Flag for the selector
   * @param  array  $options   Options for the current test
   *
   * @return sfTestFunctionalBase|sfTester
   */
  public function checkElement($selector, $value = true, $options = array())
  {
    if (is_null($this->dom))
    {
      throw new LogicException('The DOM is not accessible because the browser response content type is not HTML.');
    }

    if (is_object($selector))
    {
      $values = $selector->getValues();
    }
    else
    {
      $values = $this->domCssSelector->matchAll($selector)->getValues();
    }

    if (false === $value)
    {
      $this->tester->is(count($values), 0, sprintf('response selector "%s" does not exist', $selector));
    }
    else if (true === $value)
    {
      $this->tester->cmp_ok(count($values), '>', 0, sprintf('response selector "%s" exists', $selector));
    }
    else if (is_int($value))
    {
      $this->tester->is(count($values), $value, sprintf('response selector "%s" matches "%s" times', $selector, $value));
    }
    else if (preg_match('/^(!)?([^a-zA-Z0-9\\\\]).+?\\2[ims]?$/', $value, $match))
    {
      $position = isset($options['position']) ? $options['position'] : 0;
      if ($match[1] == '!')
      {
        $this->tester->unlike(@$values[$position], substr($value, 1), sprintf('response selector "%s" does not match regex "%s"', $selector, substr($value, 1)));
      }
      else
      {
        $this->tester->like(@$values[$position], $value, sprintf('response selector "%s" matches regex "%s"', $selector, $value));
      }
    }
    else
    {
      $position = isset($options['position']) ? $options['position'] : 0;
      $this->tester->is(@$values[$position], $value, sprintf('response selector "%s" matches "%s"', $selector, $value));
    }

    if (isset($options['count']))
    {
      $this->tester->is(count($values), $options['count'], sprintf('response selector "%s" matches "%s" times', $selector, $options['count']));
    }

    return $this->getObjectToReturn();
  }

  /**
   * Tests for a response header.
   *
   * @param  string $key
   * @param  string $value
   *
   * @return sfTestFunctionalBase|sfTester
   */
  public function isHeader($key, $value)
  {
    $headers = explode(', ', $this->response->getHttpHeader($key));
    $ok = false;
    $regex = false;
    $mustMatch = true;
    if (preg_match('/^(!)?([^a-zA-Z0-9\\\\]).+?\\2[ims]?$/', $value, $match))
    {
      $regex = $value;
      if ($match[1] == '!')
      {
        $mustMatch = false;
        $regex = substr($value, 1);
      }
    }

    foreach ($headers as $header)
    {
      if (false !== $regex)
      {
        if ($mustMatch)
        {
          if (preg_match($regex, $header))
          {
            $ok = true;
            $this->tester->pass(sprintf('response header "%s" matches "%s" (%s)', $key, $value, $this->response->getHttpHeader($key)));
            break;
          }
        }
        else
        {
          if (preg_match($regex, $header))
          {
            $ok = true;
            $this->tester->fail(sprintf('response header "%s" does not match "%s" (%s)', $key, $value, $this->response->getHttpHeader($key)));
            break;
          }
        }
      }
      else if ($header == $value)
      {
        $ok = true;
        $this->tester->pass(sprintf('response header "%s" is "%s" (%s)', $key, $value, $this->response->getHttpHeader($key)));
        break;
      }
    }

    if (!$ok)
    {
      if (!$mustMatch)
      {
        $this->tester->pass(sprintf('response header "%s" matches "%s" (%s)', $key, $value, $this->response->getHttpHeader($key)));
      }
      else
      {
        $this->tester->fail(sprintf('response header "%s" matches "%s" (%s)', $key, $value, $this->response->getHttpHeader($key)));
      }
    }

    return $this->getObjectToReturn();
  }

  /**
   * Tests if a cookie was set.
   * 
   * @param  string $name
   * @param  string $value
   * @param  array  $attributes Other cookie attributes to check (expires, path, domain, etc)
   * 
   * @return sfTestFunctionalBase|sfTester
   */
  public function setsCookie($name, $value = null, $attributes = array())
  {
    foreach ($this->response->getCookies() as $cookie)
    {
      if ($name == $cookie['name'])
      {
        if (is_null($value))
        {
          $this->tester->pass(sprintf('response sets cookie "%s"', $name));
        }
        else
        {
          $this->tester->ok($value == $cookie['value'], sprintf('response sets cookie "%s" to "%s"', $name, $value));
        }

        foreach ($attributes as $attributeName => $attributeValue)
        {
          if (!array_key_exists($attributeName, $cookie))
          {
            throw new LogicException(sprintf('The cookie attribute "%s" is not valid.', $attributeName));
          }

          $this->tester->is($cookie[$attributeName], $attributeValue, sprintf('"%s" cookie "%s" attribute is "%s"', $name, $attributeName, $attributeValue));
        }

        return $this->getObjectToReturn();
      }
    }

    $this->tester->fail(sprintf('response sets cookie "%s"', $name));

    return $this->getObjectToReturn();
  }

  /**
   * Tests whether or not a given string is in the response.
   *
   * @param string Text to check
   *
   * @return sfTestFunctionalBase|sfTester
   */
  public function contains($text)
  {
    $this->tester->like($this->response->getContent(), '/'.preg_quote($text, '/').'/', sprintf('response contains "%s"', substr($text, 0, 40)));

    return $this->getObjectToReturn();
  }

  /**
   * Tests the status code.
   *
   * @param string $statusCode Status code to check, default 200
   *
   * @return sfTestFunctionalBase|sfTester
   */
  public function isStatusCode($statusCode = 200)
  {
    $this->tester->is($this->response->getStatusCode(), $statusCode, sprintf('status code is "%s"', $statusCode));

    return $this->getObjectToReturn();
  }

  /**
   * Tests if the current request has been redirected.
   *
   * @param  bool $boolean  Flag for redirection mode
   *
   * @return sfTestFunctionalBase|sfTester
   */
  public function isRedirected($boolean = true)
  {
    if ($location = $this->response->getHttpHeader('location'))
    {
      $boolean ? $this->tester->pass(sprintf('page redirected to "%s"', $location)) : $this->tester->fail(sprintf('page redirected to "%s"', $location));
    }
    else
    {
      $boolean ? $this->tester->fail('page redirected') : $this->tester->pass('page not redirected');
    }

    return $this->getObjectToReturn();
  }

  /**
   * Outputs some debug information about the current response.
   */
  public function debug()
  {
    print $this->tester->error('Response debug');

    printf("HTTP/1.X %s\n", $this->response->getStatusCode());

    foreach ($this->response->getHttpHeaders() as $name => $value)
    {
      printf("%s: %s\n", $name, $value);
    }

    foreach ($this->response->getCookies() as $cookie)
    {
      vprintf("Set-Cookie: %s=%s; %spath=%s%s%s%s\n", array(
        $cookie['name'],
        $cookie['value'],
        is_null($cookie['expire']) ? '' : sprintf('expires=%s; ', date('D d-M-Y H:i:s T', $cookie['expire'])),
        $cookie['path'],
        $cookie['domain'] ? sprintf('; domain=%s', $cookie['domain']) : '',
        $cookie['secure'] ? '; secure' : '',
        $cookie['httpOnly'] ? '; HttpOnly' : '',
      ));
    }

    echo "\n";
    echo $this->response->getContent();

    exit(1);
  }
}
