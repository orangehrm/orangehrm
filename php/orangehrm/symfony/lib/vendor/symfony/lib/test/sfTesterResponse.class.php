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
 * @version    SVN: $Id: sfTesterResponse.class.php 27061 2010-01-22 17:08:04Z FabianLange $
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
      $this->dom = new DOMDocument('1.0', $this->response->getCharset());
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
    if (null === $this->dom)
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
   * Checks that a form is rendered correctly.
   * 
   * @param  sfForm|string $form     A form object or the name of a form class
   * @param  string        $selector CSS selector for the root form element for this form
   * 
   * @return sfTestFunctionalBase|sfTester
   */
  public function checkForm($form, $selector = 'form')
  {
    if (!$form instanceof sfForm)
    {
      $form = new $form();
    }

    $rendered = array();
    foreach ($this->domCssSelector->matchAll(sprintf('%1$s input, %1$s textarea, %1$s select', $selector))->getNodes() as $element)
    {
      $rendered[] = $element->getAttribute('name');
    }

    foreach ($form as $field => $widget)
    {
      $dom = new DOMDocument('1.0', sfConfig::get('sf_charset'));
      $dom->loadHTML((string) $widget);

      foreach ($dom->getElementsByTagName('*') as $element)
      {
        if (in_array($element->tagName, array('input', 'select', 'textarea')))
        {
          if (false !== $pos = array_search($element->getAttribute('name'), $rendered))
          {
            unset($rendered[$pos]);
          }

          $this->tester->ok(false !== $pos, sprintf('response includes "%s" form "%s" field - "%s %s[name=%s]"', get_class($form), $field, $selector, $element->tagName, $element->getAttribute('name')));
        }
      }
    }

    return $this->getObjectToReturn();
  }

  /**
   * Validates the response.
   *
   * @param mixed $checkDTD Either true to validate against the response DTD or
   *                        provide the path to a *.xsd, *.rng or *.rnc schema
   *
   * @return sfTestFunctionalBase|sfTester
   *
   * @throws LogicException If the response is neither XML nor (X)HTML
   */
  public function isValid($checkDTD = false)
  {
    if (preg_match('/(x|ht)ml/i', $this->response->getContentType()))
    {
      $revert = libxml_use_internal_errors(true);

      $dom = new DOMDocument('1.0', $this->response->getCharset());
      $content = $this->response->getContent();

      if (true === $checkDTD)
      {
        $cache = sfConfig::get('sf_cache_dir').'/sf_tester_response/w3';
        if ($cache[1] == ':')
        {
          // On Windows systems the path will be like c:\symfony\cache\xml.dtd
          // I did not manage to get DOMDocument loading a file protocol url including the drive letter
          // file://c:\symfony\cache\xml.dtd or file://c:/symfony/cache/xml.dtd
          // The first one simply doesnt work, the second one is treated as remote call.
          // However the following works. Unfortunatly this means we can only access the current disk
          // file:///symfony/cache/xml.dtd
          // Note that all work for file_get_contents so the bug is most likely in DOMDocument.
          $local = 'file://'.substr(str_replace(DIRECTORY_SEPARATOR, '/', $cache), 2);
        }
        else
        {
          $local = 'file://'.$cache;
        }

        if (!file_exists($cache.'/TR/xhtml11/DTD/xhtml11.dtd'))
        {
          $filesystem = new sfFilesystem();

          $finder = sfFinder::type('any')->discard('.sf');
          $filesystem->mirror(dirname(__FILE__).'/w3', $cache, $finder);

          $finder = sfFinder::type('file');
          $filesystem->replaceTokens($finder->in($cache), '##', '##', array('LOCAL_W3' => $local));
        }

        $content = preg_replace('#(<!DOCTYPE[^>]+")http://www.w3.org(.*")#i', '\\1'.$local.'\\2', $content);
        $dom->validateOnParse = $checkDTD;
      }

      $dom->loadXML($content);

      switch (pathinfo($checkDTD, PATHINFO_EXTENSION))
      {
        case 'xsd':
          $dom->schemaValidate($checkDTD);
          $message = sprintf('response validates per XSD schema "%s"', basename($checkDTD));
          break;
        case 'rng':
        case 'rnc':
          $dom->relaxNGValidate($checkDTD);
          $message = sprintf('response validates per relaxNG schema "%s"', basename($checkDTD));
          break;
        default:
          $message = $dom->validateOnParse ? sprintf('response validates as "%s"', $dom->doctype->name) : 'response is well-formed "xml"';
      }

      if (count($errors = libxml_get_errors()))
      {
        $lines = explode(PHP_EOL, $this->response->getContent());

        $this->tester->fail($message);
        foreach ($errors as $error)
        {
          $this->tester->diag('    '.trim($error->message));
          if (preg_match('/line (\d+)/', $error->message, $match) && $error->line != $match[1])
          {
            $this->tester->diag('      '.str_pad($match[1].':', 6).trim($lines[$match[1] - 1]));
          }
          $this->tester->diag('      '.str_pad($error->line.':', 6).trim($lines[$error->line - 1]));
        }
      }
      else
      {
        $this->tester->pass($message);
      }

      libxml_use_internal_errors($revert);
    }
    else
    {
      throw new LogicException(sprintf('Unable to validate responses of content type "%s"', $this->response->getContentType()));
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
        if (null === $value)
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
   * Tests the response content against a regex.
   *
   * @param string Regex
   *
   * @return sfTestFunctionalBase|sfTester
   */
  public function matches($regex)
  {
    if (!preg_match('/^(!)?([^a-zA-Z0-9\\\\]).+?\\2[ims]?$/', $regex, $match))
    {
      throw new InvalidArgumentException(sprintf('"%s" is not a valid regular expression.', $regex));
    }

    if ($match[1] == '!')
    {
      $this->tester->unlike($this->response->getContent(), substr($regex, 1), sprintf('response content does not match regex "%s"', substr($regex, 1)));
    }
    else
    {
      $this->tester->like($this->response->getContent(), $regex, sprintf('response content matches regex "%s"', $regex));
    }

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
   *
   * @param string $realOutput Whether to display the actual content of the response when an error occurred
   *                           or the exception message and the stack trace to ease debugging
   */
  public function debug($realOutput = false)
  {
    print $this->tester->error('Response debug');

    if (!$realOutput && null !== sfException::getLastException())
    {
      // print the exception and the stack trace instead of the "normal" output
      $this->tester->comment('WARNING');
      $this->tester->comment('An error occurred when processing this request.');
      $this->tester->comment('The real response content has been replaced with the exception message to ease debugging.');
    }

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
        null === $cookie['expire'] ? '' : sprintf('expires=%s; ', date('D d-M-Y H:i:s T', $cookie['expire'])),
        $cookie['path'],
        $cookie['domain'] ? sprintf('; domain=%s', $cookie['domain']) : '',
        $cookie['secure'] ? '; secure' : '',
        $cookie['httpOnly'] ? '; HttpOnly' : '',
      ));
    }

    echo "\n";
    if (!$realOutput && null !== $exception = sfException::getLastException())
    {
      echo $exception;
    }
    else
    {
      echo $this->response->getContent();
    }
    echo "\n";

    exit(1);
  }
}
