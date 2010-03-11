<?php
/**
 * Include the jquery.js core whenever the jQuery helper is needed
 */
sfContext::getInstance()->getResponse()->addJavascript(sfConfig::get('app_sfJQueryPlugin_library_path', 'jq/jquery'), 'first');

/**
   * Periodically calls the specified url ('url') every 'frequency' seconds (default is 10).
   * Usually used to update a specified div ('update') with the results of the remote call.
   * The options for specifying the target with 'url' and defining callbacks is the same as 'link_to_remote()'.
   */
function jq_periodically_call_remote($options = array())
{
  $frequency = isset($options['frequency']) ? $options['frequency'] : 10; // every ten seconds by default
  $code = 'setInterval(function() {'.jq_remote_function($options).'}, '.($frequency * 1000).')';

  return jq_javascript_tag($code);
}

/**
   * Returns a link that'll trigger a javascript function using the
   * onclick handler and return false after the fact.
   *
   * Examples:
   *   <?php echo link_to_function('Greeting', "alert('Hello world!')") ?>
   *   <?php echo link_to_function(image_tag('delete'), "if confirm('Really?'){ do_delete(); }") ?>
   */
function jq_link_to_function($name, $function, $html_options = array())
{
  $html_options = _parse_attributes($html_options);

  $html_options['href'] = isset($html_options['href']) ? $html_options['href'] : '#';
  $html_options['onclick'] = $function.'; return false;';

  return content_tag('a', $name, $html_options);
}

/**
   * Returns a link to a remote action defined by 'url'
   * (using the 'url_for()' format) that's called in the background using
   * XMLHttpRequest. The result of that request can then be inserted into a
   * DOM object whose id can be specified with 'update'.
   * Usually, the result would be a partial prepared by the controller with
   * either 'render_partial()'.
   *
   * Examples:
   *  <?php echo link_to_remote('Delete this post'), array(
   *    'update' => 'posts',
   *    'url'    => 'destroy?id='.$post.id,
   *  )) ?>
   *  <?php echo link_to_remote(image_tag('refresh'), array(
   *    'update' => 'emails',
   *    'url'    => '@list_emails',
   *  )) ?>
   *
   * You can also specify a hash for 'update' to allow for
   * easy redirection of output to an other DOM element if a server-side error occurs:
   *
   * Example:
   *  <?php echo link_to_remote('Delete this post', array(
   *      'update' => array('success' => 'posts', 'failure' => 'error'),
   *      'url'    => 'destroy?id='.$post.id,
   *  )) ?>
   *
   * Optionally, you can use the 'position' parameter to influence
   * how the target DOM element is updated. It must be one of
   * 'before', 'top', 'bottom', or 'after'.
   *
   * By default, these remote requests are processed asynchronous during
   * which various JavaScript callbacks can be triggered (for progress indicators and
   * the likes). All callbacks get access to the 'request' object,
   * which holds the underlying XMLHttpRequest.
   *
   * To access the server response, use 'request.responseText', to
   * find out the HTTP status, use 'request.status'.
   *
   * If you are using JSON, you can access it via the 'data' parameter
   *
   * Example:
   *  <?php echo jq_link_to_remote($word, array(
   *    'url'      => '@undo?n='.$word_counter,
   *    'complete' => 'undoRequestCompleted(request)'
   *  )) ?>
   *
   * The callbacks that may be specified are (in order):
   *
   * 'loading'                 Called when the remote document is being
   *                           loaded with data by the browser.
   * 'loaded'                  Called when the browser has finished loading
   *                           the remote document.
   * 'interactive'             Called when the user can interact with the
   *                           remote document, even though it has not
   *                           finished loading.
   * 'success'                 Called when the XMLHttpRequest is completed,
   *                           and the HTTP status code is in the 2XX range.
   * 'failure'                 Called when the XMLHttpRequest is completed,
   *                           and the HTTP status code is not in the 2XX
   *                           range.
   * 'complete'                Called when the XMLHttpRequest is complete
   *                           (fires after success/failure if they are present).,
   *
   * You can further refine 'success' and 'failure' by adding additional
   * callbacks for specific status codes:
   *
   * Example:
   *  <?php echo jq_link_to_remote($word, array(
   *       'url'     => '@rule',
   *       '404'     => "alert('Not found...? Wrong URL...?')",
   *       'failure' => "alert('HTTP Error ' + request.status + '!')",
   *  )) ?>
   *
   * A status code callback overrides the success/failure handlers if present.
   *
   * If you for some reason or another need synchronous processing (that'll
   * block the browser while the request is happening), you can specify
   * 'type' => 'synchronous'.
   *
   * You can customize further browser side call logic by passing
   * in JavaScript code snippets via some optional parameters. In
   * their order of use these are:
   *
   * 'confirm'             Adds confirmation dialog.
   * 'condition'           Perform remote request conditionally
   *                       by this expression. Use this to
   *                       describe browser-side conditions when
   *                       request should not be initiated.
   * 'before'              Called before request is initiated.
   * 'after'               Called immediately after request was
   *                       initiated and before 'loading'.
   * 'submit'              Specifies the DOM element ID that's used
   *                       as the parent of the form elements. By
   *                       default this is the current form, but
   *                       it could just as well be the ID of a
   *                       table row or any other DOM element.
   */
function jq_link_to_remote($name, $options = array(), $html_options = array())
{
  return jq_link_to_function($name, jq_remote_function($options), $html_options);
}

/**
   * Returns a Javascript function (or expression) that will update a DOM element '$element_id'
   * according to the '$options' passed.
   *
   * Possible '$options' are:
   * 'content'            The content to use for updating. Can be left out if using block, see example.
   * 'action'             Valid options are 'update' (assumed by default), 'empty', 'remove'
   * 'position'           If the 'action' is 'update', you can optionally specify one of the following positions:
   *                      'before', 'top', 'bottom', 'after'.
   *
   * Example:
   *   <?php echo jq_javascript_tag(
   *      jq_update_element_function('products', array(
   *            'position' => 'bottom',
   *            'content'  => "<p>New product!</p>",
   *      ))
   *   ) ?>
   */
function jq_update_element_function($element_id, $options = array())
{
  $content = escape_javascript(isset($options['content']) ? $options['content'] : '');

  $value = isset($options['action']) ? $options['action'] : 'update';
  switch ($value)
  {
    case 'update':
      $updateMethod = _update_method(isset($options['position']) ? $options['position'] : '');
      $javascript_function = "jQuery('#$element_id').$updateMethod('$content')";
      break;

    case 'empty':
      $javascript_function = "jQuery('#$element_id').empty()";
      break;

    case 'remove':
      $javascript_function = "jQuery('#$element_id').remove()";
      break;

    default:
      throw new sfException('Invalid action, choose one of update, remove, empty');
  }

  $javascript_function .= ";\n";

  return (isset($options['binding']) ? $javascript_function.$options['binding'] : $javascript_function);
}

/**
   * Returns the javascript needed for a remote function.
   * Takes the same arguments as 'jq_link_to_remote()'.
   *
   * Example:
   *   <select id="options" onchange="<?php echo remote_function(array('update' => 'options', 'url' => '@update_options')) ?>">
   *     <option value="0">Hello</option>
   *     <option value="1">World</option>
   *   </select>
   */
function jq_remote_function($options)
{

  // Defining elements to update
  if (isset($options['update']) && is_array($options['update']))
  {
    // On success, update the element with returned data
    if (isset($options['update']['success'])) $update_success = "#".$options['update']['success'];

    // On failure, execute a client-side function
    if (isset($options['update']['failure'])) $update_failure = $options['update']['failure'];
  }
  else if (isset($options['update'])) $update_success = "#".$options['update'];

  // Update method
  $updateMethod = _update_method(isset($options['position']) ? $options['position'] : '');

  // Callbacks
  if (isset($options['loading'])) $callback_loading = $options['loading'];
  if (isset($options['complete'])) $callback_complete = $options['complete'];
  if (isset($options['success'])) $callback_success = $options['success'];

  $execute = 'false';
  if ((isset($options['script'])) && ($options['script'] == '1')) $execute = 'true';

  // Data Type
  if (isset($options['dataType']))
  {
    $dataType = $options['dataType'];
  }
  elseif ($execute)
  {
    $dataType = 'html';
  }
  else
  {
    $dataType = 'text';
  }

  // POST or GET ?
  $method = 'POST';
  if ((isset($options['method'])) && (strtoupper($options['method']) == 'GET')) $method = $options['method'];

  // async or sync, async is default
  if ((isset($options['type'])) && ($options['type'] == 'synchronous')) $type = 'false';

  // Is it a form submitting
  if (isset($options['form'])) $formData = 'jQuery(this).serialize()';
  elseif (isset($options['submit'])) $formData = '{\'#'.$options['submit'].'\'}.serialize()';
  elseif (isset($options['with'])) $formData = '\''.$options['with'].'\'';

  // build the function
  $function = "jQuery.ajax({";
  $function .= 'type:\''.$method.'\'';
  $function .= ',dataType:\'' . $dataType . '\'';
  if (isset($type)) $function .= ',async:'.$type;
  if (isset($formData)) $function .= ',data:'.$formData;
  if (isset($update_success) and !isset($callback_success)) $function .= ',success:function(data, textStatus){jQuery(\''.$update_success.'\').'.$updateMethod.'(data);}';
  if (isset($update_failure)) $function .= ',error:function(XMLHttpRequest, textStatus, errorThrown){'.$update_failure.'}';
  if (isset($callback_loading)) $function .= ',beforeSend:function(XMLHttpRequest){'.$callback_loading.'}';
  if (isset($callback_complete)) $function .= ',complete:function(XMLHttpRequest, textStatus){'.$callback_complete.'}';
  if (isset($callback_success)) $function .= ',success:function(data, textStatus){'.$callback_success.'}';
  $function .= ',url:\''.url_for($options['url']).'\'';
  $function .= '})';

  if (isset($options['before']))
  {
    $function = $options['before'].'; '.$function;
  }
  if (isset($options['after']))
  {
    $function = $function.'; '.$options['after'];
  }
  if (isset($options['condition']))
  {
    $function = 'if ('.$options['condition'].') { '.$function.'; }';
  }
  if (isset($options['confirm']))
  {
    $function = "if (confirm('".escape_javascript($options['confirm'])."')) { $function; }";
    if (isset($options['cancel']))
    {
      $function = $function.' else { '.$options['cancel'].' }';
    }
  }

  return $function;
}

/**
   * Returns a form tag that will submit using XMLHttpRequest in the background instead of the regular
   * reloading POST arrangement. Even though it's using JavaScript to serialize the form elements, the form submission
   * will work just like a regular submission as viewed by the receiving side (all elements available in 'params').
   * The options for specifying the target with 'url' and defining callbacks are the same as 'link_to_remote()'.
   *
   * A "fall-through" target for browsers that don't do JavaScript can be specified
   * with the 'action'/'method' options on '$options_html'
   *
   * Example:
   *  <?php echo jq_form_remote_tag(array(
   *    'url'      => '@tag_add',
   *    'update'   => 'question_tags',
   *    'loading'  => "Element.show('indicator'); \$('tag').value = ''",
   *    'complete' => "Element.hide('indicator');".visual_effect('highlight', 'question_tags'),
   *  )) ?>
   *
   * The hash passed as a second argument is equivalent to the options (2nd) argument in the form_tag() helper.
   *
   * By default the fall-through action is the same as the one specified in the 'url'
   * (and the default method is 'post').
   */
function jq_form_remote_tag($options = array(), $options_html = array())
{
  $options = _parse_attributes($options);
  $options_html = _parse_attributes($options_html);

  $options['form'] = true;

  $options_html['onsubmit'] = jq_remote_function($options).'; return false;';
  $options_html['action'] = isset($options_html['action']) ? $options_html['action'] : url_for($options['url']);
  $options_html['method'] = isset($options_html['method']) ? $options_html['method'] : 'post';

  return tag('form', $options_html, true);
}

/**
   *  Returns a button input tag that will submit form using XMLHttpRequest in the background instead of regular
   *  reloading POST arrangement. The '$options' argument is the same as in 'jq_form_remote_tag()'.
   */
function jq_submit_to_remote($name, $value, $options = array(), $options_html = array())
{
  $options = _parse_attributes($options);
  $options_html = _parse_attributes($options_html);

  if (!isset($options['with']))
  {
    $options['with'] = 'jQuery(this.form.elements).serialize()';
  }

  $options_html['type'] = 'button';
  $options_html['onclick'] = jq_remote_function($options).'; return false;';
  $options_html['name'] = $name;
  $options_html['value'] = $value;

  return tag('input', $options_html, false);
}

/**
   * Returns a JavaScript tag with the '$content' inside.
   * Example:
   *   <?php echo jq_javascript_tag("alert('All is good')") ?>
   *   => <script type="text/javascript">alert('All is good')</script>
   */
function jq_javascript_tag($content)
{
  return content_tag('script', jq_javascript_cdata_section($content), array('type' => 'text/javascript'));
}

function jq_javascript_cdata_section($content)
{
  return "\n//".cdata_section("\n$content\n//")."\n";
}

function _jq_options_for_javascript($options)
{
  $opts = array();
  foreach ($options as $key => $value)
  {
    $opts[] = "$key:$value";
  }
  sort($opts);

  return '{'.join(', ', $opts).'}';
}

function _update_method($position) {
  // Updating method
  $updateMethod = 'html';
  switch ($position) {
    case 'before':$updateMethod='before';break;
    case 'after':$updateMethod='after';break;
    case 'top':$updateMethod='prepend';break;
    case 'bottom':$updateMethod='append';break;
  }

  return $updateMethod;
}
