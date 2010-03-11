<?php

/*
 * This file is part of the symfony package.
 * (c) 2006 Pierre Minnieur <pm@pierre-minnieur.de>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Enter description here ...
 * 
 * @package    symfony
 * @subpackage helper
 * @author     Pierre Minnieur <pm@pierre-minnieur.de>
 * @author     Nick Winfield <enquiries@superhaggis.com>
 * @author     Dave Dash <dave (dot) dash /at/ spindrop . us>
 * @version    SVN: $Id: YUIConnectionHelper.php 3577 2007-03-06 17:14:54Z davedash $
 */

/**
 * Returns a valuable list of available callbacks.
 * 
 * @return array $callbacks
 */
function yui_get_callbacks()
{
  static $callbacks;
  if (!$callbacks)
  {
    $callbacks = array('success', 'failure', 'argument', 'scope', 'upload');
  }

  return $callbacks;
}

/**
 * Prepares a YAHOO.util.Connect.asyncRequest call which can be called via
 * an event handler or callback.
 * 
 * Possible callback cases:
 * 
 *   - success
 *   - failure
 *   - scope
 *   - upload
 *   - argument
 * 
 * Example usage:
 * 
 *   <?php use_helper('YUIConnection'); ?>
 *   ...
 *   <script language="javascript" type="text/javascript">
 *     var transaction = <?php echo yui_connection('GET', 'default/index', array(
 *       'success' => "function(o) { alert(o.responseText) }",
 *       'failure' => "myOwnFailureFunction(o)",
 *     )); ?>
 *   </script>
 *
 * @param string $method HTTP transaction method
 * @param string $uri Fully qualified path of resource
 * @param array $callbacks User-defined callback function or object 
 * @param string $postData Optional POST body
 * @return string $js
 * @see http://developer.yahoo.com/yui/docs/YAHOO.util.Connect.html#asyncRequest
 */
function yui_connection($method = 'POST', $uri, $callbacks = array(), $postData = null)
{
  sfYUI::addComponent('yahoo');
  sfYUI::addComponent('connection');
  
  // is $uri a route?
  if (substr($uri, 0, 1) == '@')
  {
    $uri = url_for($uri);
  }
  
  // extract post data
  if ($method == 'POST' && !$postData and strstr($uri, '?'))
  {
    $postData = substr($uri, strpos($uri, '?') + 1);
    $uri      = substr($uri, 0, strpos($uri, '?'));
  }
  
  $js = '';

  $js .= "var connection = YAHOO.util.Connect; ";
  $js .= "connection.initHeader('X-Requested-With', 'XMLHttpRequest'); ";  
  $js .= "connection.asyncRequest('" . $method . "', '" . $uri . "', ";
  
  // callbacks
  $js .= "{ ";
  foreach (yui_get_callbacks() as $callback)
  {
    // valid callback?
    if (isset($callbacks[$callback]))
    {
      $js .= $callback . ": ";

      // this maybe has to be completed
      switch ($callback)
      {
        case 'success':
        case 'failure':
        case 'scope':
        case 'upload':
          $js .= $callbacks[$callback];
          break;
        case 'argument':
          if (is_array($callbacks[$callback]))
          {
            $js .= "[ ";
            foreach ($callbacks[$callback] as $argument)
            {
              $js .= $argument . ", ";
            }
            $js .= " ]";
          }
          break;
      }
      
      $js .= ", ";
    }
  }
  $js .= " }";
  
  // append post data
  if ($postData)
  {
    $js .= ", '" . $postData . "'";
  }
  
  $js .= ")";
  
  return $js;
}

/**
 * Returns a link to a remote action defined by 'url'
 * (using the 'url_for()' format).
 *
 * Example usage:
 *
 *   <div id="results"></div>
 *   <?php echo yui_link_to_remote('Grab results', array(
 *     'url' => 'results/fetch',
 *     'success' => "function(o) { document.getElementById('results').innerHTML = o.responseText; }",
 *     'failure' => "function(o) { alert('unable to fetch the data: ' + o.statusText); }",
 *   )) ?>
 *
 * @param string $name
 * @param array $options
 * @see link_to_function()
 */
function yui_link_to_remote($name, $options = array(), $html_options = array())
{
  $url = '';
  $method = 'GET';
  $callbacks = array();
  $postData = '';

  if (isset($options['method']))
  {
    $method = $options['method'];    
  }

  if (isset($options['url']))
  {
    $url = $options['url'];
  }

  if (isset($options['success']))
  {
    $callbacks['success'] = $options['success'];
  }
  
  if (isset($options['failure']))
  {
    $callbacks['failure'] = $options['failure'];
  }

  if (isset($options['postData']))
  {
    $postData = $options['postData'];
  }

  return link_to_function($name, yui_connection($method, $url, $callbacks, $postData), $html_options);  
}

/**
 * Returns a form tag that makes an ajax request onchange that updates a specified element
 *
 * Example usage:
 *   
 *   <div id="results"></div>
 *   <?php echo yui_live_form_tag('@form_submit', array(
 *     'url' => '@live_form',
 *     'update' => 'results'
 *   )) ?>
 *
 *  @form_submit does the final submission
 *  @live_form takes a dynamic submission and 
 *
 * @param string $submit_route
 * @param array $options
 * @see link_to_function()
 */

	function yui_live_form_tag($submit_route, $options = array())
	{

		sfYUI::addComponent('yahoo');
	  sfYUI::addComponent('connection');
    $options = _parse_attributes($options);

		$url = '';
		if (isset($options['url']))
		{
			$url = url_for($options['url']);
			unset($options['url']);
		}
		$update = '';
		if (isset($options['update']))
		{
			$update = $options['update'];
			unset($options['update']);
		}
		
		$func = 'new function(){var t=event.target||event.srcElement;var c=YAHOO.util.Connect;c.setForm(t.form);';
		$func .= 'c.initHeader(\'X-Requested-With\', \'XMLHttpRequest\');';
		$func .= 'c.asyncRequest("POST", "'.$url.'",{success:function(o){document.getElementById("'.$update.'").innerHTML=o.responseText;}});}';
		$options['onchange'] = $func;
		return form_tag($submit_route, $options);
	}
	
	/**
	 * Returns a text input tag that makes an ajax request onchange that 
	 *	updates a specified element
	 *
	 * Example usage:
	 *   
	 *   <div id="results"></div>
	 *   <?php echo yui_live_input_tag('@form_submit', array(
	 *     'url' => '@live_form',
	 *     'update' => 'results'
	 *   )) ?>
	 *
	 *  @form_submit does the final submission
	 *  @live_form takes a dynamic submission and 
	 *
	 * This is useful for live javascript validation.
	 *
	 * @param string $name
	 * @param string $default
	 * @param array $options
	 * @see input_tag
	 */

		function yui_live_input_tag($name, $default = null, $options = array())
		{

			sfYUI::addComponent('yahoo');
		  sfYUI::addComponent('connection');
	    $options = _parse_attributes($options);

			$url = '';
			if (isset($options['url']))
			{
				$url = url_for($options['url']);
				unset($options['url']);
			}
			$update = '';
			if (isset($options['update']))
			{
				$update = $options['update'];
				unset($options['update']);
			}

			$func = 'new function(){c=YAHOO.util.Connect;';
			$func .= 'c.initHeader(\'X-Requested-With\', \'XMLHttpRequest\');';
			$func .= 'c.asyncRequest("POST", "'.$url.'",{success:function(o){document.getElementById("'.$update.'").innerHTML=o.responseText;}},"value="+event.target.value);}';
			$options['onchange'] = $func;
			return input_tag($name, $default, $options);
		}