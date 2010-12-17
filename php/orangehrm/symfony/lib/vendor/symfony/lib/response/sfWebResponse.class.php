<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWebResponse class.
 *
 * This class manages web reponses. It supports cookies and headers management.
 *
 * @package    symfony
 * @subpackage response
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfWebResponse.class.php 28347 2010-03-02 17:47:14Z fabien $
 */
class sfWebResponse extends sfResponse
{
  const
    FIRST  = 'first',
    MIDDLE = '',
    LAST   = 'last',
    ALL    = 'ALL',
    RAW    = 'RAW';

  protected
    $cookies     = array(),
    $statusCode  = 200,
    $statusText  = 'OK',
    $headerOnly  = false,
    $headers     = array(),
    $metas       = array(),
    $httpMetas   = array(),
    $positions   = array('first', '', 'last'),
    $stylesheets = array(),
    $javascripts = array(),
    $slots       = array();

  static protected $statusTexts = array(
    '100' => 'Continue',
    '101' => 'Switching Protocols',
    '200' => 'OK',
    '201' => 'Created',
    '202' => 'Accepted',
    '203' => 'Non-Authoritative Information',
    '204' => 'No Content',
    '205' => 'Reset Content',
    '206' => 'Partial Content',
    '300' => 'Multiple Choices',
    '301' => 'Moved Permanently',
    '302' => 'Found',
    '303' => 'See Other',
    '304' => 'Not Modified',
    '305' => 'Use Proxy',
    '306' => '(Unused)',
    '307' => 'Temporary Redirect',
    '400' => 'Bad Request',
    '401' => 'Unauthorized',
    '402' => 'Payment Required',
    '403' => 'Forbidden',
    '404' => 'Not Found',
    '405' => 'Method Not Allowed',
    '406' => 'Not Acceptable',
    '407' => 'Proxy Authentication Required',
    '408' => 'Request Timeout',
    '409' => 'Conflict',
    '410' => 'Gone',
    '411' => 'Length Required',
    '412' => 'Precondition Failed',
    '413' => 'Request Entity Too Large',
    '414' => 'Request-URI Too Long',
    '415' => 'Unsupported Media Type',
    '416' => 'Requested Range Not Satisfiable',
    '417' => 'Expectation Failed',
    '500' => 'Internal Server Error',
    '501' => 'Not Implemented',
    '502' => 'Bad Gateway',
    '503' => 'Service Unavailable',
    '504' => 'Gateway Timeout',
    '505' => 'HTTP Version Not Supported',
  );

  /**
   * Initializes this sfWebResponse.
   *
   * Available options:
   *
   *  * charset:           The charset to use (utf-8 by default)
   *  * content_type:      The content type (text/html by default)
   *  * send_http_headers: Whether to send HTTP headers or not (true by default)
   *  * http_protocol:     The HTTP protocol to use for the response (HTTP/1.0 by default)
   *
   * @param  sfEventDispatcher $dispatcher  An sfEventDispatcher instance
   * @param  array             $options     An array of options
   *
   * @return bool true, if initialization completes successfully, otherwise false
   *
   * @throws <b>sfInitializationException</b> If an error occurs while initializing this sfResponse
   *
   * @see sfResponse
   */
  public function initialize(sfEventDispatcher $dispatcher, $options = array())
  {
    parent::initialize($dispatcher, $options);

    $this->javascripts = array_combine($this->positions, array_fill(0, count($this->positions), array()));
    $this->stylesheets = array_combine($this->positions, array_fill(0, count($this->positions), array()));

    if (!isset($this->options['charset']))
    {
      $this->options['charset'] = 'utf-8';
    }

    if (!isset($this->options['send_http_headers']))
    {
      $this->options['send_http_headers'] = true;
    }

    if (!isset($this->options['http_protocol']))
    {
      $this->options['http_protocol'] = 'HTTP/1.0';
    }

    $this->options['content_type'] = $this->fixContentType(isset($this->options['content_type']) ? $this->options['content_type'] : 'text/html');
  }

  /**
   * Sets if the response consist of just HTTP headers.
   *
   * @param bool $value
   */
  public function setHeaderOnly($value = true)
  {
    $this->headerOnly = (boolean) $value;
  }

  /**
   * Returns if the response must only consist of HTTP headers.
   *
   * @return bool returns true if, false otherwise
   */
  public function isHeaderOnly()
  {
    return $this->headerOnly;
  }

  /**
   * Sets a cookie.
   *
   * @param  string  $name      HTTP header name
   * @param  string  $value     Value for the cookie
   * @param  string  $expire    Cookie expiration period
   * @param  string  $path      Path
   * @param  string  $domain    Domain name
   * @param  bool    $secure    If secure
   * @param  bool    $httpOnly  If uses only HTTP
   *
   * @throws <b>sfException</b> If fails to set the cookie
   */
  public function setCookie($name, $value, $expire = null, $path = '/', $domain = '', $secure = false, $httpOnly = false)
  {
    if ($expire !== null)
    {
      if (is_numeric($expire))
      {
        $expire = (int) $expire;
      }
      else
      {
        $expire = strtotime($expire);
        if ($expire === false || $expire == -1)
        {
          throw new sfException('Your expire parameter is not valid.');
        }
      }
    }

    $this->cookies[$name] = array(
      'name'     => $name,
      'value'    => $value,
      'expire'   => $expire,
      'path'     => $path,
      'domain'   => $domain,
      'secure'   => $secure ? true : false,
      'httpOnly' => $httpOnly,
    );
  }

  /**
   * Sets response status code.
   *
   * @param string $code  HTTP status code
   * @param string $name  HTTP status text
   *
   */
  public function setStatusCode($code, $name = null)
  {
    $this->statusCode = $code;
    $this->statusText = null !== $name ? $name : self::$statusTexts[$code];
  }

  /**
   * Retrieves status text for the current web response.
   *
   * @return string Status text
   */
  public function getStatusText()
  {
    return $this->statusText;
  }

  /**
   * Retrieves status code for the current web response.
   *
   * @return integer Status code
   */
  public function getStatusCode()
  {
    return $this->statusCode;
  }

  /**
   * Sets a HTTP header.
   *
   * @param string  $name     HTTP header name
   * @param string  $value    Value (if null, remove the HTTP header)
   * @param bool    $replace  Replace for the value
   *
   */
  public function setHttpHeader($name, $value, $replace = true)
  {
    $name = $this->normalizeHeaderName($name);

    if (null === $value)
    {
      unset($this->headers[$name]);

      return;
    }

    if ('Content-Type' == $name)
    {
      if ($replace || !$this->getHttpHeader('Content-Type', null))
      {
        $this->setContentType($value);
      }

      return;
    }

    if (!$replace)
    {
      $current = isset($this->headers[$name]) ? $this->headers[$name] : '';
      $value = ($current ? $current.', ' : '').$value;
    }

    $this->headers[$name] = $value;
  }

  /**
   * Gets HTTP header current value.
   *
   * @param  string $name     HTTP header name
   * @param  string $default  Default value returned if named HTTP header is not found
   *
   * @return string
   */
  public function getHttpHeader($name, $default = null)
  {
    $name = $this->normalizeHeaderName($name);

    return isset($this->headers[$name]) ? $this->headers[$name] : $default;
  }

  /**
   * Checks if response has given HTTP header.
   *
   * @param  string $name  HTTP header name
   *
   * @return bool
   */
  public function hasHttpHeader($name)
  {
    return array_key_exists($this->normalizeHeaderName($name), $this->headers);
  }

  /**
   * Sets response content type.
   *
   * @param string $value  Content type
   *
   */
  public function setContentType($value)
  {
    $this->headers['Content-Type'] = $this->fixContentType($value);
  }

  /**
   * Gets the current charset as defined by the content type.
   *
   * @return string The current charset
   */
  public function getCharset()
  {
    return $this->options['charset'];
  }

  /**
   * Gets response content type.
   *
   * @return array
   */
  public function getContentType()
  {
    return $this->getHttpHeader('Content-Type', $this->options['content_type']);
  }

  /**
   * Sends HTTP headers and cookies. Only the first invocation of this method will send the headers.
   * Subsequent invocations will silently do nothing. This allows certain actions to send headers early,
   * while still using the standard controller.
   */
  public function sendHttpHeaders()
  {
    if (!$this->options['send_http_headers'])
    {
      return;
    }

    // status
    $status = $this->options['http_protocol'].' '.$this->statusCode.' '.$this->statusText;
    header($status);

    if (substr(php_sapi_name(), 0, 3) == 'cgi')
    {
      // fastcgi servers cannot send this status information because it was sent by them already due to the HTT/1.0 line
      // so we can safely unset them. see ticket #3191
      unset($this->headers['Status']);
    }

    if ($this->options['logging'])
    {
      $this->dispatcher->notify(new sfEvent($this, 'application.log', array(sprintf('Send status "%s"', $status))));
    }

    // headers
    if (!$this->getHttpHeader('Content-Type'))
    {
      $this->setContentType($this->options['content_type']);
    }
    foreach ($this->headers as $name => $value)
    {
      header($name.': '.$value);

      if ($value != '' && $this->options['logging'])
      {
        $this->dispatcher->notify(new sfEvent($this, 'application.log', array(sprintf('Send header "%s: %s"', $name, $value))));
      }
    }

    // cookies
    foreach ($this->cookies as $cookie)
    {
      setrawcookie($cookie['name'], $cookie['value'], $cookie['expire'], $cookie['path'], $cookie['domain'], $cookie['secure'], $cookie['httpOnly']);

      if ($this->options['logging'])
      {
        $this->dispatcher->notify(new sfEvent($this, 'application.log', array(sprintf('Send cookie "%s": "%s"', $cookie['name'], $cookie['value']))));
      }
    }
    // prevent resending the headers
    $this->options['send_http_headers'] = false;
  }

  /**
   * Send content for the current web response.
   *
   */
  public function sendContent()
  {
    if (!$this->headerOnly)
    {
      parent::sendContent();
    }
  }

  /**
   * Sends the HTTP headers and the content.
   */
  public function send()
  {
    $this->sendHttpHeaders();
    $this->sendContent();
  }

  /**
   * Retrieves a normalized Header.
   *
   * @param  string $name  Header name
   *
   * @return string Normalized header
   */
  protected function normalizeHeaderName($name)
  {
    return preg_replace('/\-(.)/e', "'-'.strtoupper('\\1')", strtr(ucfirst(strtolower($name)), '_', '-'));
  }

  /**
   * Retrieves a formated date.
   *
   * @param  string $timestamp  Timestamp
   * @param  string $type       Format type
   *
   * @return string Formatted date
   */
  static public function getDate($timestamp, $type = 'rfc1123')
  {
    $type = strtolower($type);

    if ($type == 'rfc1123')
    {
      return substr(gmdate('r', $timestamp), 0, -5).'GMT';
    }
    else if ($type == 'rfc1036')
    {
      return gmdate('l, d-M-y H:i:s ', $timestamp).'GMT';
    }
    else if ($type == 'asctime')
    {
      return gmdate('D M j H:i:s', $timestamp);
    }
    else
    {
      throw new InvalidArgumentException('The second getDate() method parameter must be one of: rfc1123, rfc1036 or asctime.');
    }
  }

  /**
   * Adds vary to a http header.
   *
   * @param string $header  HTTP header
   */
  public function addVaryHttpHeader($header)
  {
    $vary = $this->getHttpHeader('Vary');
    $currentHeaders = array();
    if ($vary)
    {
      $currentHeaders = preg_split('/\s*,\s*/', $vary);
    }
    $header = $this->normalizeHeaderName($header);

    if (!in_array($header, $currentHeaders))
    {
      $currentHeaders[] = $header;
      $this->setHttpHeader('Vary', implode(', ', $currentHeaders));
    }
  }

  /**
   * Adds an control cache http header.
   *
   * @param string $name   HTTP header
   * @param string $value  Value for the http header
   */
  public function addCacheControlHttpHeader($name, $value = null)
  {
    $cacheControl = $this->getHttpHeader('Cache-Control');
    $currentHeaders = array();
    if ($cacheControl)
    {
      foreach (preg_split('/\s*,\s*/', $cacheControl) as $tmp)
      {
        $tmp = explode('=', $tmp);
        $currentHeaders[$tmp[0]] = isset($tmp[1]) ? $tmp[1] : null;
      }
    }
    $currentHeaders[strtr(strtolower($name), '_', '-')] = $value;

    $headers = array();
    foreach ($currentHeaders as $key => $value)
    {
      $headers[] = $key.(null !== $value ? '='.$value : '');
    }

    $this->setHttpHeader('Cache-Control', implode(', ', $headers));
  }

  /**
   * Retrieves meta headers for the current web response.
   *
   * @return string Meta headers
   */
  public function getHttpMetas()
  {
    return $this->httpMetas;
  }

  /**
   * Adds a HTTP meta header.
   *
   * @param string  $key      Key to replace
   * @param string  $value    HTTP meta header value (if null, remove the HTTP meta)
   * @param bool    $replace  Replace or not
   */
  public function addHttpMeta($key, $value, $replace = true)
  {
    $key = $this->normalizeHeaderName($key);

    // set HTTP header
    $this->setHttpHeader($key, $value, $replace);

    if (null === $value)
    {
      unset($this->httpMetas[$key]);

      return;
    }

    if ('Content-Type' == $key)
    {
      $value = $this->getContentType();
    }
    elseif (!$replace)
    {
      $current = isset($this->httpMetas[$key]) ? $this->httpMetas[$key] : '';
      $value = ($current ? $current.', ' : '').$value;
    }

    $this->httpMetas[$key] = $value;
  }

  /**
   * Retrieves all meta headers.
   *
   * @return array List of meta headers
   */
  public function getMetas()
  {
    return $this->metas;
  }

  /**
   * Adds a meta header.
   *
   * @param string  $key      Name of the header
   * @param string  $value    Meta header value (if null, remove the meta)
   * @param bool    $replace  true if it's replaceable
   * @param bool    $escape   true for escaping the header
   */
  public function addMeta($key, $value, $replace = true, $escape = true)
  {
    $key = strtolower($key);

    if (null === $value)
    {
      unset($this->metas[$key]);

      return;
    }

    // FIXME: If you use the i18n layer and escape the data here, it won't work
    // see include_metas() in AssetHelper
    if ($escape)
    {
      $value = htmlspecialchars($value, ENT_QUOTES, $this->options['charset']);
    }

    $current = isset($this->metas[$key]) ? $this->metas[$key] : null;
    if ($replace || !$current)
    {
      $this->metas[$key] = $value;
    }
  }

  /**
   * Retrieves title for the current web response.
   *
   * @return string Title
   */
  public function getTitle()
  {
    return isset($this->metas['title']) ? $this->metas['title'] : '';
  }

  /**
   * Sets title for the current web response.
   *
   * @param string  $title   Title name
   * @param bool    $escape  true, for escaping the title
   */
  public function setTitle($title, $escape = true)
  {
    $this->addMeta('title', $title, true, $escape);
  }

  /**
   * Returns the available position names for stylesheets and javascripts in order.
   *
   * @return array An array of position names
   */
  public function getPositions()
  {
    return $this->positions;
  }

  /**
   * Retrieves stylesheets for the current web response.
   *
   * By default, the position is sfWebResponse::ALL,
   * and the method returns all stylesheets ordered by position.
   *
   * @param  string  $position The position
   *
   * @return array   An associative array of stylesheet files as keys and options as values
   */
  public function getStylesheets($position = self::ALL)
  {
    if (self::ALL === $position)
    {
      $stylesheets = array();
      foreach ($this->getPositions() as $position)
      {
        foreach ($this->stylesheets[$position] as $file => $options)
        {
          $stylesheets[$file] = $options;
        }
      }

      return $stylesheets;
    }
    else if (self::RAW === $position)
    {
      return $this->stylesheets;
    }

    $this->validatePosition($position);

    return $this->stylesheets[$position];
  }

  /**
   * Adds a stylesheet to the current web response.
   *
   * @param string $file      The stylesheet file
   * @param string $position  Position
   * @param string $options   Stylesheet options
   */
  public function addStylesheet($file, $position = '', $options = array())
  {
    $this->validatePosition($position);

    $this->stylesheets[$position][$file] = $options;
  }

  /**
   * Removes a stylesheet from the current web response.
   *
   * @param string $file The stylesheet file to remove
   */
  public function removeStylesheet($file)
  {
    foreach ($this->getPositions() as $position)
    {
      unset($this->stylesheets[$position][$file]);
    }
  }

  /**
   * Retrieves javascript files from the current web response.
   *
   * By default, the position is sfWebResponse::ALL,
   * and the method returns all javascripts ordered by position.
   *
   * @param  string $position  The position
   *
   * @return array An associative array of javascript files as keys and options as values
   */
  public function getJavascripts($position = self::ALL)
  {
    if (self::ALL === $position)
    {
      $javascripts = array();
      foreach ($this->getPositions() as $position)
      {
        foreach ($this->javascripts[$position] as $file => $options)
        {
          $javascripts[$file] = $options;
        }
      }

      return $javascripts;
    }
    else if (self::RAW === $position)
    {
      return $this->javascripts;
    }

    $this->validatePosition($position);

    return $this->javascripts[$position];
  }

  /**
   * Adds javascript code to the current web response.
   *
   * @param string $file      The JavaScript file
   * @param string $position  Position
   * @param string $options   Javascript options
   */
  public function addJavascript($file, $position = '', $options = array())
  {
    $this->validatePosition($position);

    $this->javascripts[$position][$file] = $options;
  }

  /**
   * Removes a JavaScript file from the current web response.
   *
   * @param string $file The Javascript file to remove
   */
  public function removeJavascript($file)
  {
    foreach ($this->getPositions() as $position)
    {
      unset($this->javascripts[$position][$file]);
    }
  }

  /**
   * Retrieves slots from the current web response.
   *
   * @return string Javascript code
   */
  public function getSlots()
  {
    return $this->slots;
  }

  /**
   * Sets a slot content.
   *
   * @param string $name     Slot name
   * @param string $content  Content
   */
  public function setSlot($name, $content)
  {
    $this->slots[$name] = $content;
  }

  /**
   * Retrieves cookies from the current web response.
   *
   * @return array Cookies
   */
  public function getCookies()
  {
    return $this->cookies;
  }

  /**
   * Retrieves HTTP headers from the current web response.
   *
   * @return string HTTP headers
   */
  public function getHttpHeaders()
  {
    return $this->headers;
  }

  /**
   * Cleans HTTP headers from the current web response.
   */
  public function clearHttpHeaders()
  {
    $this->headers = array();
  }

  /**
   * Copies all properties from a given sfWebResponse object to the current one.
   *
   * @param sfWebResponse $response  An sfWebResponse instance
   */
  public function copyProperties(sfWebResponse $response)
  {
    $this->options     = $response->getOptions();
    $this->headers     = $response->getHttpHeaders();
    $this->metas       = $response->getMetas();
    $this->httpMetas   = $response->getHttpMetas();
    $this->stylesheets = $response->getStylesheets(self::RAW);
    $this->javascripts = $response->getJavascripts(self::RAW);
    $this->slots       = $response->getSlots();
  }

  /**
   * Merges all properties from a given sfWebResponse object to the current one.
   *
   * @param sfWebResponse $response  An sfWebResponse instance
   */
  public function merge(sfWebResponse $response)
  {
    foreach ($this->getPositions() as $position)
    {
      $this->javascripts[$position] = array_merge($this->getJavascripts($position), $response->getJavascripts($position));
      $this->stylesheets[$position] = array_merge($this->getStylesheets($position), $response->getStylesheets($position));
    }

    $this->slots = array_merge($this->getSlots(), $response->getSlots());
  }

  /**
   * @see sfResponse
   */
  public function serialize()
  {
    return serialize(array($this->content, $this->statusCode, $this->statusText, $this->options, $this->headerOnly, $this->headers, $this->metas, $this->httpMetas, $this->stylesheets, $this->javascripts, $this->slots));
  }

  /**
   * @see sfResponse
   */
  public function unserialize($serialized)
  {
    list($this->content, $this->statusCode, $this->statusText, $this->options, $this->headerOnly, $this->headers, $this->metas, $this->httpMetas, $this->stylesheets, $this->javascripts, $this->slots) = unserialize($serialized);
  }

  /**
   * Validate a position name.
   *
   * @param  string $position
   *
   * @throws InvalidArgumentException if the position is not available
   */
  protected function validatePosition($position)
  {
    if (!in_array($position, $this->positions, true))
    {
      throw new InvalidArgumentException(sprintf('The position "%s" does not exist (available positions: %s).', $position, implode(', ', $this->positions)));
    }
  }

  /**
   * Fixes the content type by adding the charset for text content types.
   *
   * @param  string $contentType  The content type
   *
   * @return string The content type with the charset if needed
   */
  protected function fixContentType($contentType)
  {
    // add charset if needed (only on text content)
    if (false === stripos($contentType, 'charset') && (0 === stripos($contentType, 'text/') || strlen($contentType) - 3 === strripos($contentType, 'xml')))
    {
      $contentType .= '; charset='.$this->options['charset'];
    }

    // change the charset for the response
    if (preg_match('/charset\s*=\s*(.+)\s*$/', $contentType, $match))
    {
      $this->options['charset'] = $match[1];
    }

    return $contentType;
  }
}
