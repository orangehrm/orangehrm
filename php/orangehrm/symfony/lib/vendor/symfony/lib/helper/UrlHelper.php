<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * UrlHelper.
 *
 * @package    symfony
 * @subpackage helper
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: UrlHelper.php 27753 2010-02-08 19:24:39Z Kris.Wallsmith $
 */

function link_to2($name, $routeName, $params, $options = array())
{
  $params = array_merge(array('sf_route' => $routeName), is_object($params) ? array('sf_subject' => $params) : $params);

  return link_to1($name, $params, $options);
}

function link_to1($name, $internal_uri, $options = array())
{
  $html_options = _parse_attributes($options);

  $html_options = _convert_options_to_javascript($html_options);

  $absolute = false;
  if (isset($html_options['absolute_url']))
  {
    $html_options['absolute'] = $html_options['absolute_url'];
    unset($html_options['absolute_url']);
  }
  if (isset($html_options['absolute']))
  {
    $absolute = (boolean) $html_options['absolute'];
    unset($html_options['absolute']);
  }

  $html_options['href'] = url_for($internal_uri, $absolute);

  if (isset($html_options['query_string']))
  {
    $html_options['href'] .= '?'.$html_options['query_string'];
    unset($html_options['query_string']);
  }

  if (isset($html_options['anchor']))
  {
    $html_options['href'] .= '#'.$html_options['anchor'];
    unset($html_options['anchor']);
  }

  if (is_object($name))
  {
    if (method_exists($name, '__toString'))
    {
      $name = $name->__toString();
    }
    else
    {
      throw new sfException(sprintf('Object of class "%s" cannot be converted to string (Please create a __toString() method).', get_class($name)));
    }
  }

  if (!strlen($name))
  {
    $name = $html_options['href'];
  }

  return content_tag('a', $name, $html_options);
}

function url_for2($routeName, $params = array(), $absolute = false)
{
  $params = array_merge(array('sf_route' => $routeName), is_object($params) ? array('sf_subject' => $params) : $params);

  return url_for1($params, $absolute);
}

function url_for1($internal_uri, $absolute = false)
{
  return sfContext::getInstance()->getController()->genUrl($internal_uri, $absolute);
}

/**
 * Returns a routed URL based on the module/action passed as argument
 * and the routing configuration.
 *
 * <b>Examples:</b>
 * <code>
 *  echo url_for('my_module/my_action');
 *    => /path/to/my/action
 *  echo url_for('@my_rule');
 *    => /path/to/my/action 
 *  echo url_for('@my_rule', true);
 *    => http://myapp.example.com/path/to/my/action
 * </code>
 *
 * @param  string $internal_uri  'module/action' or '@rule' of the action
 * @param  bool   $absolute      return absolute path?
 * @return string routed URL
 */
function url_for()
{
  // for BC with 1.1
  $arguments = func_get_args();
  if (is_array($arguments[0]) || '@' == substr($arguments[0], 0, 1) || false !== strpos($arguments[0], '/'))
  {
    return call_user_func_array('url_for1', $arguments);
  }
  else
  {
    return call_user_func_array('url_for2', $arguments);
  }
}

/**
 * Creates a <a> link tag of the given name using a routed URL
 * based on the module/action passed as argument and the routing configuration.
 * It's also possible to pass a string instead of a module/action pair to
 * get a link tag that just points without consideration. 
 * If null is passed as a name, the link itself will become the name.
 * If an object is passed as a name, the object string representation is used.
 * One of the options serves for for creating javascript confirm alerts where 
 * if you pass 'confirm' => 'Are you sure?', the link will be guarded 
 * with a JS popup asking that question. If the user accepts, the link is processed,
 * otherwise not.
 *
 * <b>Options:</b>
 * - 'absolute' - if set to true, the helper outputs an absolute URL
 * - 'query_string' - to append a query string (starting by ?) to the routed url
 * - 'anchor' - to append an anchor (starting by #) to the routed url
 * - 'confirm' - displays a javascript confirmation alert when the link is clicked
 * - 'popup' - if set to true, the link opens a new browser window 
 * - 'post' - if set to true, the link submits a POST request instead of GET (caution: do not use inside a form)
 * - 'method' - if set to post, delete, or put, the link submits a request with the given HTTP method instead of GET (caution: do not use inside a form)
 *
 * <b>Note:</b> The 'popup', 'post', and 'method' options are not compatible with each other.
 *
 * <b>Examples:</b>
 * <code>
 *  echo link_to('Delete this page', 'my_module/my_action');
 *    => <a href="/path/to/my/action">Delete this page</a>
 *  echo link_to('Visit Hoogle', 'http://www.hoogle.com');
 *    => <a href="http://www.hoogle.com">Visit Hoogle</a>
 *  echo link_to('Delete this page', 'my_module/my_action', array('id' => 'myid', 'confirm' => 'Are you sure?', 'absolute' => true));
 *    => <a href="http://myapp.example.com/path/to/my/action" id="myid" onclick="return confirm('Are you sure?');">Delete this page</a>
 * </code>
 *
 * @param  string $name          name of the link, i.e. string to appear between the <a> tags
 * @param  string $internal_uri  'module/action' or '@rule' of the action
 * @param  array  $options       additional HTML compliant <a> tag parameters
 * @return string XHTML compliant <a href> tag
 * @see    url_for
 */
function link_to()
{
  // for BC with 1.1
  $arguments = func_get_args();
  if (empty($arguments[1]) || is_array($arguments[1]) || '@' == substr($arguments[1], 0, 1) || false !== strpos($arguments[1], '/'))
  {
    return call_user_func_array('link_to1', $arguments);
  }
  else
  {
    if (!array_key_exists(2, $arguments))
    {
      $arguments[2] = array();
    }
    return call_user_func_array('link_to2', $arguments);
  }
}

function url_for_form(sfFormObject $form, $routePrefix)
{
  $format = '%s/%s';
  if ('@' == $routePrefix[0])
  {
    $format = '%s_%s';
    $routePrefix = substr($routePrefix, 1);
  }

  $uri = sprintf($format, $routePrefix, $form->getObject()->isNew() ? 'create' : 'update');

  return url_for($uri, $form->getObject());
}

function form_tag_for(sfForm $form, $routePrefix, $attributes = array())
{
  return $form->renderFormTag(url_for_form($form, $routePrefix), $attributes);
}

/**
 * If the condition passed as first argument is true,
 * creates a <a> link tag of the given name using a routed URL
 * based on the module/action passed as argument and the routing configuration.
 * If the condition is false, the given name is returned between <span> tags
 *
 * <b>Options:</b>
 * - 'tag' - the HTML tag that must enclose the name if the condition is false, defaults to <span>
 * - 'absolute' - if set to true, the helper outputs an absolute URL
 * - 'query_string' - to append a query string (starting by ?) to the routed url
 * - 'anchor' - to append an anchor (starting by #) to the routed url
 * - 'confirm' - displays a javascript confirmation alert when the link is clicked
 * - 'popup' - if set to true, the link opens a new browser window 
 * - 'post' - if set to true, the link submits a POST request instead of GET (caution: do not use inside a form)
 *
 * <b>Examples:</b>
 * <code>
 *  echo link_to_if($user->isAdministrator(), 'Delete this page', 'my_module/my_action');
 *    => <a href="/path/to/my/action">Delete this page</a>
 *  echo link_to_if(!$user->isAdministrator(), 'Delete this page', 'my_module/my_action'); 
 *    => <span>Delete this page</span>
 * </code>
 *
 * @param  bool   $condition     condition
 * @param  string $name          name of the link, i.e. string to appear between the <a> tags
 * @param  string $internal_uri  'module/action' or '@rule' of the action
 * @param  array  $options       additional HTML compliant <a> tag parameters
 *
 * @return string XHTML compliant <a href> tag or name
 *
 * @see    link_to
 */
function link_to_if()
{
  $arguments = func_get_args();
  if (empty($arguments[2]) || '@' == substr($arguments[2], 0, 1) || false !== strpos($arguments[2], '/'))
  {
    list($condition, $name, $params, $options) = array_pad($arguments, 4, null);
  }
  else
  {
    list($condition, $name, $routeName, $params, $options) = array_pad($arguments, 5, null);
    $params = array_merge(array('sf_route' => $routeName), is_object($params) ? array('sf_subject' => $params) : (array) $params);
  }

  $html_options = _parse_attributes($options);
  if ($condition)
  {
    unset($html_options['tag']);
    return link_to1($name, $params, $html_options);
  }
  else
  {
    unset($html_options['query_string']);
    unset($html_options['absolute_url']);
    unset($html_options['absolute']);

    $tag = _get_option($html_options, 'tag', 'span');

    return content_tag($tag, $name, $html_options);
  }
}

/**
 * If the condition passed as first argument is false,
 * creates a <a> link tag of the given name using a routed URL
 * based on the module/action passed as argument and the routing configuration.
 * If the condition is true, the given name is returned between <span> tags
 *
 * <b>Options:</b>
 * - 'tag' - the HTML tag that must enclose the name if the condition is true, defaults to <span>
 * - 'absolute' - if set to true, the helper outputs an absolute URL
 * - 'query_string' - to append a query string (starting by ?) to the routed url
 * - 'anchor' - to append an anchor (starting by #) to the routed url
 * - 'confirm' - displays a javascript confirmation alert when the link is clicked
 * - 'popup' - if set to true, the link opens a new browser window 
 * - 'post' - if set to true, the link submits a POST request instead of GET (caution: do not use inside a form)
 *
 * <b>Examples:</b>
 * <code>
 *  echo link_to_unless($user->isAdministrator(), 'Delete this page', 'my_module/my_action');
 *    => <span>Delete this page</span>
 *  echo link_to_unless(!$user->isAdministrator(), 'Delete this page', 'my_module/my_action'); 
 *    => <a href="/path/to/my/action">Delete this page</a>
 * </code>
 *
 * @param  bool   $condition     condition
 * @param  string $name          name of the link, i.e. string to appear between the <a> tags
 * @param  string $internal_uri  'module/action' or '@rule' of the action
 * @param  array  $options       additional HTML compliant <a> tag parameters
 *
 * @return string XHTML compliant <a href> tag or name
 *
 * @see    link_to
 */
function link_to_unless()
{
  $arguments = func_get_args();
  $arguments[0] = !$arguments[0];
  return call_user_func_array('link_to_if', $arguments);
}

/**
 * Returns a URL rooted at the web root
 *
 * @param   string  $path     The route to append 
 * @param   bool    $absolute If true, an absolute path is returned (optional)
 * @return  The web URL root 
 */
function public_path($path, $absolute = false)
{
  $request = sfContext::getInstance()->getRequest();
  $root = $request->getRelativeUrlRoot();

  if ($absolute)
  {
    $source = 'http';
    if ($request->isSecure())
    {
      $source .= 's';
    }
    $source .='://'.$request->getHost().$root;
  }
  else
  {
    $source = $root;
  }
  
  if (substr($path, 0, 1) != '/')
  {
    $path = '/'.$path;
  }

  return $source.$path;
}

/**
 * Creates an <input> button tag of the given name pointing to a routed URL
 * based on the module/action passed as argument and the routing configuration.
 * The syntax is similar to the one of link_to.
 *
 * <b>Options:</b>
 * - 'absolute' - if set to true, the helper outputs an absolute URL
 * - 'query_string' - to append a query string (starting by ?) to the routed url
 * - 'anchor' - to append an anchor (starting by #) to the routed url
 * - 'confirm' - displays a javascript confirmation alert when the button is clicked
 * - 'popup' - if set to true, the button opens a new browser window 
 * - 'post' - if set to true, the button submits a POST request instead of GET (caution: do not use inside a form)
 *
 * <b>Examples:</b>
 * <code>
 *  echo button_to('Delete this page', 'my_module/my_action');
 *    => <input value="Delete this page" type="button" onclick="document.location.href='/path/to/my/action';" />
 * </code>
 *
 * @param  string $name          name of the button
 * @param  string $internal_uri  'module/action' or '@rule' of the action
 * @param  array  $options       additional HTML compliant <input> tag parameters
 * @return string XHTML compliant <input> tag
 * @see    url_for, link_to
 */
function button_to($name, $internal_uri, $options = array())
{
  $html_options = _parse_attributes($options);
  $html_options['value'] = $name;

  if (isset($html_options['post']) && $html_options['post'])
  {
    if (isset($html_options['popup']))
    {
      throw new sfConfigurationException('You can\'t use "popup" and "post" together.');
    }
    $html_options['type'] = 'submit';
    unset($html_options['post']);
    $html_options = _convert_options_to_javascript($html_options);

    return form_tag($internal_uri, array('method' => 'post', 'class' => 'button_to')).content_tag('div', tag('input', $html_options)).'</form>';
  }

  $url = url_for($internal_uri);
  if (isset($html_options['query_string']))
  {
    $url = $url.'?'.$html_options['query_string'];
    unset($html_options['query_string']);
  }
  if (isset($html_options['anchor']))
  {
    $url = $url.'#'.$html_options['anchor'];
    unset($html_options['anchor']);
  }
  $url = "'".$url."'";
  $html_options['type'] = 'button';

  if (isset($html_options['popup']))
  {
    $html_options = _convert_options_to_javascript($html_options, $url);
    unset($html_options['popup']);
  }
  else
  {
    $html_options['onclick'] = "document.location.href=".$url.";";
    $html_options = _convert_options_to_javascript($html_options);
  }

  return tag('input', $html_options);
}

/**
 * Returns an HTML <form> tag that points to a valid action, route or URL as defined by <i>$url_for_options</i>.
 *
 * By default, the form tag is generated in POST format, but can easily be configured along with any additional
 * HTML parameters via the optional <i>$options</i> parameter. If you are using file uploads, be sure to set the 
 * <i>multipart</i> option to true.
 *
 * <b>Options:</b>
 * - multipart - When set to true, enctype is set to "multipart/form-data".
 *
 * <b>Examples:</b>
 *   <code><?php echo form_tag('@myroute'); ?></code>
 *   <code><?php echo form_tag('/module/action', array('name' => 'myformname', 'multipart' => true)); ?></code>
 *
 * @param  string $url_for_options  valid action, route or URL
 * @param  array  $options          optional HTML parameters for the <form> tag
 *
 * @return string opening HTML <form> tag with options
 */
function form_tag($url_for_options = '', $options = array())
{
  $options = _parse_attributes($options);

  $html_options = $options;

  $html_options['method'] = isset($html_options['method']) ? strtolower($html_options['method']) : 'post';

  if (_get_option($html_options, 'multipart'))
  {
    $html_options['enctype'] = 'multipart/form-data';
  }

  $html_options['action'] = url_for($url_for_options);

  $html = '';
  if (!in_array($html_options['method'], array('get', 'post')))
  {
    $html = tag('input', array('type' => 'hidden', 'name' => 'sf_method', 'value' => $html_options['method']));
    $html_options['method'] = 'post';
  }

  return tag('form', $html_options, true).$html;
}

/**
 * Creates a <a> link tag to the given email (with href="mailto:...").
 * If null is passed as a name, the email itself will become the name.
 *
 * <b>Options:</b>
 * - 'encode' - if set to true, the email address appears with various random encoding for each letter.
 * The mail link still works when encoded, but the address doesn't appear in clear
 * in the source. Use it to prevent spam (efficiency not guaranteed).
 *
 * <b>Examples:</b>
 * <code>
 *  echo mail_to('webmaster@example.com');
 *    => <a href="mailto:webmaster@example.com">webmaster@example.com</a>
 *  echo mail_to('webmaster@example.com', 'send us an email');
 *    => <a href="mailto:webmaster@example.com">send us an email</a>
 *  echo mail_to('webmaster@example.com', 'send us an email', array('encode' => true));
 *    => <a href="
            &#x6d;a&#x69;&#x6c;&#x74;&#111;&#58;&#x77;&#x65;b&#x6d;as&#116;&#x65;&#114;
            &#64;&#101;&#x78;&#x61;&#x6d;&#x70;&#108;&#x65;&#46;&#99;&#x6f;&#109;
          ">send us an email</a>
 * </code>
 *
 * @param  string $email          target email
 * @param  string $name           name of the link, i.e. string to appear between the <a> tags
 * @param  array  $options        additional HTML compliant <a> tag parameters
 * @param  array  $default_value
 * @return string XHTML compliant <a href> tag
 * @see    link_to
 */
function mail_to($email, $name = '', $options = array(), $default_value = array())
{
  $html_options = _parse_attributes($options);

  $html_options = _convert_options_to_javascript($html_options);

  $default_tmp = _parse_attributes($default_value);
  $default = array();
  foreach ($default_tmp as $key => $value)
  {
    $default[] = urlencode($key).'='.urlencode($value);
  }
  $options = count($default) ? '?'.implode('&', $default) : '';

  if (isset($html_options['encode']) && $html_options['encode'])
  {
    unset($html_options['encode']);
    $html_options['href'] = _encodeText('mailto:'.$email.$options);
    if (!$name)
    {
      $name = _encodeText($email);
    }
  }
  else
  {
    $html_options['href'] = 'mailto:'.$email.$options;
    if (!$name)
    {
      $name = $email;
    }
  }

  return content_tag('a', $name, $html_options);
}

function _convert_options_to_javascript($html_options, $url = 'this.href')
{
  // confirm
  $confirm = isset($html_options['confirm']) ? $html_options['confirm'] : '';
  unset($html_options['confirm']);

  // popup
  $popup = isset($html_options['popup']) ? $html_options['popup'] : '';
  unset($html_options['popup']);

  // method
  $method = isset($html_options['method']) ? $html_options['method'] : (isset($html_options['post']) && $html_options['post'] ? 'post' : false);
  unset($html_options['post'], $html_options['method']);

  $onclick = isset($html_options['onclick']) ? $html_options['onclick'] : '';

  if ($popup && $method)
  {
    throw new sfConfigurationException('You can\'t use "popup", "method" and "post" in the same link.');
  }
  else if ($confirm && $popup)
  {
    $html_options['onclick'] = $onclick.'if ('._confirm_javascript_function($confirm).') { '._popup_javascript_function($popup, $url).' };return false;';
  }
  else if ($confirm && $method)
  {
    $html_options['onclick'] = $onclick.'if ('._confirm_javascript_function($confirm).') { '._method_javascript_function($method).' };return false;';
  }
  else if ($confirm)
  {
    if ($onclick)
    {
      $html_options['onclick'] = 'if ('._confirm_javascript_function($confirm).') { return '.$onclick.'} else return false;';
    }
    else
    {
      $html_options['onclick'] = 'return '._confirm_javascript_function($confirm).';';
    }
  }
  else if ($method)
  {
    $html_options['onclick'] = $onclick._method_javascript_function($method).'return false;';
  }
  else if ($popup)
  {
    $html_options['onclick'] = $onclick._popup_javascript_function($popup, $url).'return false;';
  }

  return $html_options;
}

function _confirm_javascript_function($confirm)
{
  return "confirm('".escape_javascript($confirm)."')";
}

function _popup_javascript_function($popup, $url = '')
{
  if (is_array($popup))
  {
    if (isset($popup[1]))
    {
      return "var w=window.open(".$url.",'".$popup[0]."','".$popup[1]."');w.focus();";
    }
    else
    {
      return "var w=window.open(".$url.",'".$popup[0]."');w.focus();";
    }
  }
  else
  {
    return "var w=window.open(".$url.");w.focus();";
  }
}

function _post_javascript_function()
{
  return _method_javascript_function('POST');
}

function _method_javascript_function($method)
{
  $function = "var f = document.createElement('form'); f.style.display = 'none'; this.parentNode.appendChild(f); f.method = 'post'; f.action = this.href;";

  if ('post' != strtolower($method))
  {
    $function .= "var m = document.createElement('input'); m.setAttribute('type', 'hidden'); ";
    $function .= sprintf("m.setAttribute('name', 'sf_method'); m.setAttribute('value', '%s'); f.appendChild(m);", strtolower($method));
  }

  // CSRF protection
  $form = new BaseForm();
  if ($form->isCSRFProtected())
  {
    $function .= "var m = document.createElement('input'); m.setAttribute('type', 'hidden'); ";
    $function .= sprintf("m.setAttribute('name', '%s'); m.setAttribute('value', '%s'); f.appendChild(m);", $form->getCSRFFieldName(), $form->getCSRFToken());
  }

  $function .= "f.submit();";

  return $function;
}

function _encodeText($text)
{
  $encoded_text = '';

  for ($i = 0; $i < strlen($text); $i++)
  {
    $char = $text{$i};
    $r = rand(0, 100);

    # roughly 10% raw, 45% hex, 45% dec
    # '@' *must* be encoded. I insist.
    if ($r > 90 && $char != '@')
    {
      $encoded_text .= $char;
    }
    else if ($r < 45)
    {
      $encoded_text .= '&#x'.dechex(ord($char)).';';
    }
    else
    {
      $encoded_text .= '&#'.ord($char).';';
    }
  }

  return $encoded_text;
}
