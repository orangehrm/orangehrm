<?php

/*
 * This file is part of the sfWebBrowserPlugin package.
 * (c) 2004-2006 Francois Zaninotto <francois.zaninotto@symfony-project.com>
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com> for the click-related functions
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWebBrowser provides a basic HTTP client.
 *
 * @package    sfWebBrowserPlugin
 * @author     Francois Zaninotto <francois.zaninotto@symfony-project.com>
 * @author     Tristan Rivoallan <tristan@rivoallan.net>
 * @version    0.9
 */
class sfWebBrowser
{
  protected
    $defaultHeaders          = array(),
    $stack                   = array(),
    $stackPosition           = -1,
    $responseHeaders         = array(),
    $responseCode            = '',
    $responseMessage         = '',
    $responseText            = '',
    $responseDom             = null,
    $responseDomCssSelector  = null,
    $responseXml             = null,
    $fields                  = array(),
    $urlInfo                 = array();

  public function __construct($defaultHeaders = array(), $adapterClass = null, $adapterOptions = array())
  {
    if(!$adapterClass)
    {
      if (function_exists('curl_init'))
      {
        $adapterClass = 'sfCurlAdapter';
      }
      else if(ini_get('allow_url_fopen') == 1)
      {
        $adapterClass = 'sfFopenAdapter';
      }
      else
      {
        $adapterClass = 'sfSocketsAdapter';
      }
    }
    $this->defaultHeaders = $this->fixHeaders($defaultHeaders);
    $this->adapter = new $adapterClass($adapterOptions);
  }
    
  // Browser methods
  
  /**
   * Restarts the browser
   *
   * @param array default browser options
   *
   * @return sfWebBrowser The current browser object
   */    
  public function restart($defaultHeaders = array())
  {
    $this->defaultHeaders = $this->fixHeaders($defaultHeaders);
    $this->stack          = array();
    $this->stackPosition  = -1;
    $this->urlInfo        = array();
    $this->initializeResponse();
    
    return $this;
  }

  /**
   * Sets the browser user agent name
   *
   * @param string agent name
   *
   * @return sfWebBrowser The current browser object
   */    
  public function setUserAgent($agent)
  {
    $this->defaultHeaders['User-Agent'] = $agent;
    
    return $this;
  }

  /**
   * Gets the browser user agent name
   *
   * @return string agent name
   */    
  public function getUserAgent()
  {
    return isset($this->defaultHeaders['User-Agent']) ? $this->defaultHeaders['User-Agent'] : '';
  }

  /**
   * Submits a GET request
   *
   * @param string The request uri
   * @param array  The request parameters (associative array)
   * @param array  The request headers (associative array)
   *
   * @return sfWebBrowser The current browser object
   */
  public function get($uri, $parameters = array(), $headers = array())
  {
    if ($parameters)
    {
      $uri .= ((false !== strpos($uri, '?')) ? '&' : '?') . http_build_query($parameters, '', '&');
    }
    return $this->call($uri, 'GET', array(), $headers);
  }

  public function head($uri, $parameters = array(), $headers = array())
  {
    if ($parameters)
    {
      $uri .= ((false !== strpos($uri, '?')) ? '&' : '?') . http_build_query($parameters, '', '&');
    }
    return $this->call($uri, 'HEAD', array(), $headers);
  }

  /**
   * Submits a POST request
   *
   * @param string The request uri
   * @param array  The request parameters (associative array)
   * @param array  The request headers (associative array)
   *
   * @return sfWebBrowser The current browser object
   */  
  public function post($uri, $parameters = array(), $headers = array())
  {
    return $this->call($uri, 'POST', $parameters, $headers);
  }
  
  /**
   * Submits a PUT request.
   *
   * @param string The request uri
   * @param array  The request parameters (associative array)
   * @param array  The request headers (associative array)
   *
   * @return sfWebBrowser The current browser object
   */  
  public function put($uri, $parameters = array(), $headers = array())
  {
    return $this->call($uri, 'PUT', $parameters, $headers);
  }

  /**
   * Submits a DELETE request.
   *
   * @param string The request uri
   * @param array  The request parameters (associative array)
   * @param array  The request headers (associative array)
   *
   * @return sfWebBrowser The current browser object
   */  
  public function delete($uri, $parameters = array(), $headers = array())
  {
    return $this->call($uri, 'DELETE', $parameters, $headers);
  }

  /**
   * Submits a request
   *
   * @param string  The request uri
   * @param string  The request method
   * @param array   The request parameters (associative array)
   * @param array   The request headers (associative array)
   * @param boolean To specify is the request changes the browser history
   *
   * @return sfWebBrowser The current browser object
   */  
  public function call($uri, $method = 'GET', $parameters = array(), $headers = array(), $changeStack = true)
  {
    $urlInfo = parse_url($uri);

    // Check headers
    $headers = $this->fixHeaders($headers);

    // check port
    if (isset($urlInfo['port']))
    {
      $this->urlInfo['port'] = $urlInfo['port'];
    }
    else if (!isset($this->urlInfo['port']))
    {
      $this->urlInfo['port'] = 80;
    }

    if(!isset($urlInfo['host']))
    {
      // relative link
      $uri = $this->urlInfo['scheme'].'://'.$this->urlInfo['host'].':'.$this->urlInfo['port'].'/'.$uri;
    }
    else if($urlInfo['scheme'] != 'http' && $urlInfo['scheme'] != 'https')
    {
      throw new Exception('sfWebBrowser handles only http and https requests'); 
    }

    $this->urlInfo = parse_url($uri);

    $this->initializeResponse();

    if ($changeStack)
    {
      $this->addToStack($uri, $method, $parameters, $headers);
    }

    $browser = $this->adapter->call($this, $uri, $method, $parameters, $headers);

    // redirect support
    if ((in_array($browser->getResponseCode(), array(301, 307)) && in_array($method, array('GET', 'HEAD'))) || in_array($browser->getResponseCode(), array(302,303)))
    {
      $this->call($browser->getResponseHeader('Location'), 'GET', array(), $headers);
    }

    return $browser;
  }

  /**
   * Gives a value to a form field in the response
   *
   * @param string field name
   * @param string field value
   *
   * @return sfWebBrowser The current browser object
   */ 
  public function setField($name, $value)
  {
    // as we don't know yet the form, just store name/value pairs
    $this->parseArgumentAsArray($name, $value, $this->fields);

    return $this;
  }
  
  /**
   * Looks for a link or a button in the response and submits the related request
   *
   * @param string The link/button value/href/alt
   * @param array request parameters (associative array)
   *
   * @return sfWebBrowser The current browser object
   */ 
  public function click($name, $arguments = array())
  {
    if (!$dom = $this->getResponseDom())
    {
      throw new Exception('Cannot click because there is no current page in the browser');
    }

    $xpath = new DomXpath($dom);

    // text link, the name being in an attribute
    if ($link = $xpath->query(sprintf('//a[@*="%s"]', $name))->item(0))
    {
      return $this->get($link->getAttribute('href'));
    }

    // text link, the name being the text value
    if ($links = $xpath->query('//a[@href]'))
    {
      foreach($links as $link)
      {
        if(preg_replace(array('/\s{2,}/', '/\\r\\n|\\n|\\r/'), array(' ', ''), $link->nodeValue) == $name)
        {
          return $this->get($link->getAttribute('href'));  
        }
      }
    }
    
    // image link, the name being the alt attribute value
    if ($link = $xpath->query(sprintf('//a/img[@alt="%s"]/ancestor::a', $name))->item(0))
    {
      return $this->get($link->getAttribute('href'));
    }

    // form, the name being the button or input value
    if (!$form = $xpath->query(sprintf('//input[((@type="submit" or @type="button") and @value="%s") or (@type="image" and @alt="%s")]/ancestor::form', $name, $name))->item(0))
    {
      throw new Exception(sprintf('Cannot find the "%s" link or button.', $name));
    }

    // form attributes
    $url = $form->getAttribute('action');
    $method = $form->getAttribute('method') ? strtolower($form->getAttribute('method')) : 'get';

    // merge form default values and arguments
    $defaults = array();
    foreach ($xpath->query('descendant::input | descendant::textarea | descendant::select', $form) as $element)
    {
      $elementName = $element->getAttribute('name');
      $nodeName    = $element->nodeName;
      $value       = null;
      if ($nodeName == 'input' && ($element->getAttribute('type') == 'checkbox' || $element->getAttribute('type') == 'radio'))
      {
        if ($element->getAttribute('checked'))
        {
          $value = $element->getAttribute('value');
        }
      }
      else if (
        $nodeName == 'input'
        &&
        (($element->getAttribute('type') != 'submit' && $element->getAttribute('type') != 'button') || $element->getAttribute('value') == $name)
        &&
        ($element->getAttribute('type') != 'image' || $element->getAttribute('alt') == $name)
      )
      {
        $value = $element->getAttribute('value');
      }
      else if ($nodeName == 'textarea')
      {
        $value = '';
        foreach ($element->childNodes as $el)
        {
          $value .= $dom->saveXML($el);
        }
      }
      else if ($nodeName == 'select')
      {
        if ($multiple = $element->hasAttribute('multiple'))
        {
          $elementName = str_replace('[]', '', $elementName);
          $value = array();
        }
        else
        {
          $value = null;
        }

        $found = false;
        foreach ($xpath->query('descendant::option', $element) as $option)
        {
          if ($option->getAttribute('selected'))
          {
            $found = true;
            if ($multiple)
            {
              $value[] = $option->getAttribute('value');
            }
            else
            {
              $value = $option->getAttribute('value');
            }
          }
        }

        // if no option is selected and if it is a simple select box, take the first option as the value
        if (!$found && !$multiple)
        {
          $value = $xpath->query('descendant::option', $element)->item(0)->getAttribute('value');
        }
      }

      if (null !== $value)
      {
        $this->parseArgumentAsArray($elementName, $value, $defaults);
      }
    }

    // create request parameters
    $arguments = sfToolkit::arrayDeepMerge($defaults, $this->fields, $arguments);
    if ('post' == $method)
    {
      return $this->post($url, $arguments);
    }
    else
    {
      return $this->get($url, $arguments);
    }
  }

  protected function parseArgumentAsArray($name, $value, &$vars)
  {
    if (false !== $pos = strpos($name, '['))
    {
      $var = &$vars;
      $tmps = array_filter(preg_split('/(\[ | \[\] | \])/x', $name));
      foreach ($tmps as $tmp)
      {
        $var = &$var[$tmp];
      }
      if ($var)
      {
        if (!is_array($var))
        {
          $var = array($var);
        }
        $var[] = $value;
      }
      else
      {
        $var = $value;
      }
    }
    else
    {
      $vars[$name] = $value;
    }
  }

  /**
   * Adds the current request to the history stack
   *
   * @param string  The request uri
   * @param string  The request method
   * @param array   The request parameters (associative array)
   * @param array   The request headers (associative array)
   *
   * @return sfWebBrowser The current browser object
   */  
  public function addToStack($uri, $method, $parameters, $headers)
  {
    $this->stack = array_slice($this->stack, 0, $this->stackPosition + 1);
    $this->stack[] = array(
      'uri'        => $uri,
      'method'     => $method,
      'parameters' => $parameters,
      'headers'    => $headers
    );
    $this->stackPosition = count($this->stack) - 1;
    
    return $this;
  }

  /**
   * Submits the previous request in history again
   *
   * @return sfWebBrowser The current browser object
   */  
  public function back()
  {
    if ($this->stackPosition < 1)
    {
      throw new Exception('You are already on the first page.');
    }

    --$this->stackPosition;
    return $this->call($this->stack[$this->stackPosition]['uri'], 
                       $this->stack[$this->stackPosition]['method'], 
                       $this->stack[$this->stackPosition]['parameters'], 
                       $this->stack[$this->stackPosition]['headers'],
                       false);
  }

  /**
   * Submits the next request in history again
   *
   * @return sfWebBrowser The current browser object
   */  
  public function forward()
  {
    if ($this->stackPosition > count($this->stack) - 2)
    {
      throw new Exception('You are already on the last page.');
    }

    ++$this->stackPosition;
    return $this->call($this->stack[$this->stackPosition]['uri'], 
                       $this->stack[$this->stackPosition]['method'], 
                       $this->stack[$this->stackPosition]['parameters'], 
                       $this->stack[$this->stackPosition]['headers'],
                       false);
  }

  /**
   * Submits the current request again
   *
   * @return sfWebBrowser The current browser object
   */  
  public function reload()
  {
    if (-1 == $this->stackPosition)
    {
      throw new Exception('No page to reload.');
    }

    return $this->call($this->stack[$this->stackPosition]['uri'], 
                       $this->stack[$this->stackPosition]['method'], 
                       $this->stack[$this->stackPosition]['parameters'], 
                       $this->stack[$this->stackPosition]['headers'],
                       false);
  }
  
  /**
   * Transforms an associative array of header names => header values to its HTTP equivalent.
   * 
   * @param    array     $headers
   * @return   string
   */
  public function prepareHeaders($headers = array())
  {
    $prepared_headers = array();
    foreach ($headers as $name => $value)
    {
      $prepared_headers[] = sprintf("%s: %s\r\n", ucfirst($name), $value);
    }
    
    return implode('', $prepared_headers);
  }
  
  // Response methods

  /**
   * Initializes the response and erases all content from prior requests
   */  
  public function initializeResponse()
  {
    $this->responseHeaders        = array();
    $this->responseCode           = '';
    $this->responseText           = '';
    $this->responseDom            = null;
    $this->responseDomCssSelector = null;
    $this->responseXml            = null;
    $this->fields                 = array();
  }

  /**
   * Set the response headers
   *
   * @param array The response headers as an array of strings shaped like "key: value"
   *
   * @return sfWebBrowser The current browser object
   */  
  public function setResponseHeaders($headers = array())
  {
    $header_array = array();
    foreach($headers as $header)
    {
      $arr = explode(': ', $header); 
      if(isset($arr[1]))
      {
        $header_array[$this->normalizeHeaderName($arr[0])] = trim($arr[1]);
      }
    }
    
    $this->responseHeaders = $header_array;
    
    return $this;
  }
  
  /**
   * Set the response code
   *
   * @param string The first line of the response
   *
   * @return sfWebBrowser The current browser object
   */  
  public function setResponseCode($firstLine)
  {
    preg_match('/\d{3}/', $firstLine, $matches);
    if(isset($matches[0]))
    {
      $this->responseCode = $matches[0];
    }
    else
    {
      $this->responseCode = '';
    }
    
    return $this;
  }  

  /**
   * Set the response contents
   *
   * @param string The response contents
   *
   * @return sfWebBrowser The current browser object
   */  
  public function setResponseText($res)
  {
    $this->responseText = $res;
    
    return $this;
  }

  /**
   * Get a text version of the response
   *
   * @return string The response contents
   */
  public function getResponseText()
  {
    $text = $this->responseText;

    // Decode any content-encoding (gzip or deflate) if needed
    switch (strtolower($this->getResponseHeader('content-encoding'))) {

        // Handle gzip encoding
        case 'gzip':
            $text = $this->decodeGzip($text);
            break;

        // Handle deflate encoding
        case 'deflate':
            $text = $this->decodeDeflate($text);
            break;

        default:
            break;
    }

    return $text;
  }

  /**
   * Get a text version of the body part of the response (without <body> and </body>)
   *
   * @return string The body part of the response contents
   */
  public function getResponseBody()
  {
    preg_match('/<body.*?>(.*)<\/body>/si', $this->getResponseText(), $matches);

    return isset($matches[1]) ? $matches[1] : '';
  }
  
  /**
   * Get a DOMDocument version of the response 
   *
   * @return DOMDocument The reponse contents
   */
  public function getResponseDom()
  {
    if(!$this->responseDom)
    {
      // for HTML/XML content, create a DOM object for the response content
      if (preg_match('/(x|ht)ml/i', $this->getResponseHeader('Content-Type')))
      {
        $this->responseDom = new DomDocument('1.0', 'utf8');
        $this->responseDom->validateOnParse = true;
        @$this->responseDom->loadHTML($this->getResponseText());
      }
    }

    return $this->responseDom;
  }

  /**
   * Get a sfDomCssSelector version of the response 
   *
   * @return sfDomCssSelector The response contents
   */
  public function getResponseDomCssSelector()
  {
    if(!$this->responseDomCssSelector)
    {
      // for HTML/XML content, create a DOM object for the response content
      if (preg_match('/(x|ht)ml/i', $this->getResponseHeader('Content-Type')))
      {
        $this->responseDomCssSelector = new sfDomCssSelector($this->getResponseDom());
      }
    }

    return $this->responseDomCssSelector;
  }

  /**
   * Get a SimpleXML version of the response 
   *
   * @return  SimpleXMLElement                      The reponse contents
   * @throws  sfWebBrowserInvalidResponseException  when response is not in a valid format 
   */
  public function getResponseXML()
  {
    if(!$this->responseXml)
    {
      // for HTML/XML content, create a DOM object for the response content
      if (preg_match('/(x|ht)ml/i', $this->getResponseHeader('Content-Type')))
      {
        $this->responseXml = @simplexml_load_string($this->getResponseText());
      }
    }
    
    // Throw an exception if response is not valid XML
    if (get_class($this->responseXml) != 'SimpleXMLElement')
    {
      $msg = sprintf("Response is not a valid XML string : \n%s", $this->getResponseText());
      throw new sfWebBrowserInvalidResponseException($msg);
    }
    
    return $this->responseXml;
  }

  /**
   * Returns true if server response is an error.
   * 
   * @return   bool
   */
  public function responseIsError()
  {
    return in_array((int)($this->getResponseCode() / 100), array(4, 5)); 
  }

  /**
   * Get the response headers
   *
   * @return array The response headers
   */
  public function getResponseHeaders()
  {
    return $this->responseHeaders;
  }  

  /**
   * Get a response header
   *
   * @param string The response header name
   *
   * @return string The response header value
   */
  public function getResponseHeader($key)
  {
    $normalized_key = $this->normalizeHeaderName($key);
    return (isset($this->responseHeaders[$normalized_key])) ? $this->responseHeaders[$normalized_key] : '';
  }  
  
  /**
   * Decodes gzip-encoded content ("content-encoding: gzip" response header).
   * 
   * @param       stream     $gzip_text
   * @return      string     
   */
  protected function decodeGzip($gzip_text)
  {
    return gzinflate(substr($gzip_text, 10));
  }

  /**
   * Decodes deflate-encoded content ("content-encoding: deflate" response header).
   * 
   * @param       stream     $deflate_text
   * @return      string     
   */  
  protected function decodeDeflate($deflate_text)
  {
    return gzuncompress($deflate_text);
  }
  
  /**
   * Get the response code
   *
   * @return string The response code
   */
  public function getResponseCode()
  {
    return $this->responseCode; 
  }
  
  /**
   * Returns the response message (the 'Not Found' part in  'HTTP/1.1 404 Not Found')
   * 
   * @return   string 
   */
  public function getResponseMessage()
  {
    return $this->responseMessage;    
  }
  
  /**
   * Sets response message.
   * 
   * @param    string    $message
   */
  public function setResponseMessage($msg)
  {
    $this->responseMessage = $msg;
  }
  
  public function getUrlInfo()
  {
    return $this->urlInfo; 
  }
  
  public function getDefaultRequestHeaders()
  {
    return $this->defaultHeaders;
  }
  
  /**
   * Adds default headers to the supplied headers array.
   * 
   * @param       array    $headers
   * @return      array
   */
  public function initializeRequestHeaders($headers = array())
  {
    // Supported encodings
    $encodings = array();
    if (isset($headers['Accept-Encoding']))
    {
      $encodings = explode(',', $headers['Accept-Encoding']);
    }
    if (function_exists('gzinflate') && !in_array('gzip', $encodings))
    {
      $encodings[] = 'gzip';
    }
    if (function_exists('gzuncompress') && !in_array('deflate', $encodings))
    {
      $encodings[] = 'deflate';
    }
    
    $headers['Accept-Encoding'] = implode(',', array_unique($encodings));
    
    return $headers;
  }
  
  /**
   * Validates supplied headers and turns all names to lowercase.
   * 
   * @param     array     $headers
   * @return    array
   */
  private function fixHeaders($headers)
  {
    $fixed_headers = array();
    foreach ($headers as $name => $value)
    {
      if (!preg_match('/([a-z]*)(-[a-z]*)*/i', $name))
      {
        $msg = sprintf('Invalid header "%s"', $name);
        throw new Exception($msg);
      }
      $fixed_headers[$this->normalizeHeaderName($name)] = trim($value);
    }

    return $fixed_headers;
  }
  
  /**
   * Retrieves a normalized Header.
   *
   * @param string Header name
   *
   * @return string Normalized header
   */
  protected function normalizeHeaderName($name)
  {
    return preg_replace('/\-(.)/e', "'-'.strtoupper('\\1')", strtr(ucfirst($name), '_', '-'));
  }

}
