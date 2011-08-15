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
 * @author     Benjamin Meynell <bmeynell@colorado.edu>
 * @version    0.9
 */
class sfSocketsAdapter
{
  protected
    $options             = array(),
    $adapterErrorMessage = null,
    $browser             = null;

  public function __construct($options = array())
  {
    $this->options = $options;
  }

  /**
   * Submits a request
   *
   * @param string  The request uri
   * @param string  The request method
   * @param array   The request parameters (associative array)
   * @param array   The request headers (associative array)
   *
   * @return sfWebBrowser The current browser object
   */
  public function call($browser, $uri, $method = 'GET', $parameters = array(), $headers = array())
  {
    $m_headers = array_merge(array('Content-Type' => 'application/x-www-form-urlencoded'), $browser->getDefaultRequestHeaders(), $browser->initializeRequestHeaders($headers));
    $request_headers = $browser->prepareHeaders($m_headers);

    $url_info = parse_url($uri);

    // initialize default values
    isset($url_info['path']) ? $path = $url_info['path'] : $path = '/';
    isset($url_info['query']) ? $qstring = '?'.$url_info['query'] : $qstring = null;
    isset($url_info['port']) ? null : $url_info['port'] = 80;

    if (!$socket = @fsockopen($url_info['host'], $url_info['port'], $errno, $errstr, 15))
    {
      throw new Exception("Could not connect ($errno): $errstr");
    }

    // build request
    $request = "$method $path$qstring HTTP/1.1\r\n";
    $request .= 'Host: '.$url_info['host'].':'.$url_info['port']."\r\n";
    $request .= $request_headers;
    $request .= "Connection: Close\r\n";

    if ($method == 'PUT' && is_array($parameters) && array_key_exists('file', $parameters))
    {
      $fp = fopen($parameters['file'], 'rb');
      $sent = 0;
      $blocksize = (2 << 20); // 2MB chunks
      $filesize = filesize($parameters['file']);

      $request .= 'Content-Length: '.$filesize."\r\n";

      $request .= "\r\n";

      fwrite($socket, $request);

      while ($sent < $filesize)
      {
        $data = fread($fp, $blocksize);
        fwrite($socket, $data);
        $sent += $blocksize;
      }
      fclose($fp);
    }
    elseif ($method == 'POST' || $method == 'PUT')
    {
      $body = is_array($parameters) ? http_build_query($parameters, '', '&') : $parameters;
      $request .= 'Content-Length: '.strlen($body)."\r\n";
      $request .= "\r\n";
      $request .= $body;
    }

    $request .= "\r\n";

    fwrite($socket, $request);

    $response = '';
    $response_body = '';
    while (!feof($socket))
    {
      $response .= fgets($socket, 1024);
    }
    fclose($socket);

    // parse response components: status line, headers and body
    $response_lines = explode("\r\n", $response);

    // http status line (ie "HTTP 1.1 200 OK")
    $status_line = array_shift($response_lines);

    $start_body = false;
    $response_headers = array();
    for($i=0; $i<count($response_lines); $i++)
    {
      // grab body
      if ($start_body == true)
      {
        // ignore chunked encoding size
        if (!preg_match('@^[0-9A-Fa-f]+\s*$@', $response_lines[$i]))
        {
          $response_body .= $response_lines[$i];
        }
      }

      // body starts after first blank line
      else if ($start_body == false && $response_lines[$i] == '')
      {
        $start_body = true;
      }

      // grab headers
      else
      {
        $response_headers[] = $response_lines[$i];
      }
    }
    
    $browser->setResponseHeaders($response_headers);
    
    // grab status code
    preg_match('@(\d{3})@', $status_line, $status_code);
    if(isset($status_code[1]))
    {
      $browser->setResponseCode($status_code[1]);
    }
    $browser->setResponseText(trim($response_body));

    return $browser;
  }

}
