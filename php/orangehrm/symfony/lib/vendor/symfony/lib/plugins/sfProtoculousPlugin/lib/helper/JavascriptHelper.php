<?php

require_once dirname(__FILE__).'/../../../../helper/JavascriptBaseHelper.php';

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) 2004 David Heinemeier Hansson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * JavascriptHelper.
 *
 * @package    symfony
 * @subpackage helper
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     John Christopher <john.christopher@symfony-project.com>
 * @author     David Heinemeier Hansson
 * @author     Fabian Lange <fabian.lange@symfony-project.com>
 * @version    SVN: $Id: JavascriptHelper.php 19310 2009-06-16 12:41:07Z FabianLange $
 */

/*
 * Provides a set of helpers to call remote methods using what has been labelled 
 * AJAX[http://www.adaptivepath.com/publications/essays/archives/000385.php].
 * This means that you can call actions in your controllers without reloading the page,
 * but still update certain parts of it using injections into the DOM.
 * The common use case is having a form that adds a new element to a list without reloading the page.
 *
 * To be able to use the JavaScript helpers, you must include the Prototype JavaScript Framework
 * and for some functions script.aculo.us (which both come with symfony) on your pages.
 * Choose one of these options:
 *
 * * Use <tt><?php echo javascript_include_tag :defaults ?></tt> in the HEAD section of your page (recommended):
 *   The function will return references to the JavaScript files created by the +rails+ command in your
 *   <tt>public/javascripts</tt> directory. Using it is recommended as the browser can then cache the libraries
 *   instead of fetching all the functions anew on every request.
 * * Use <tt><?php echo javascript_include_tag 'prototype' ?></tt>: As above, but will only include the Prototype core library,
 *   which means you are able to use all basic AJAX functionality. For the script.aculo.us-based JavaScript helpers,
 *   like visual effects, autocompletion, drag and drop and so on, you should use the method described above.
 * * Use <tt><?php echo define_javascript_functions ?></tt>: this will copy all the JavaScript support functions within a single
 *   script block.
 *
 * For documentation on +javascript_include_tag+ see ActionView::Helpers::AssetTagHelper.
 *
 * If you're the visual type, there's an AJAX movie[http://www.rubyonrails.com/media/video/rails-ajax.mov] demonstrating
 * the use of form_remote_tag.
 */

  function get_callbacks()
  {
    static $callbacks;
    if (!$callbacks)
    {
      $callbacks = array_merge(array(
        'uninitialized', 'loading', 'loaded', 'interactive', 'complete', 'failure', 'success'
        ), range(100, 599));
    }

    return $callbacks;
  }

  function get_ajax_options()
  {
    static $ajax_options;
    if (!$ajax_options)
    {
      $ajax_options = array_merge(array(
        'before', 'after', 'condition', 'url', 'asynchronous', 'method',
        'insertion', 'position', 'form', 'with', 'update', 'script'
        ), get_callbacks());
    }

    return $ajax_options;
  }

  /**
   * Returns an html button to a remote action defined by 'url' (using the
   * 'url_for()' format) that's called in the background using XMLHttpRequest.
   *
   * See link_to_remote() for details.
   *
   */
  function button_to_remote($name, $options = array(), $html_options = array())
  {
    return button_to_function($name, remote_function($options), $html_options);
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
   * Example:
   *  <?php echo link_to_remote($word, array(
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
   *  <?php echo link_to_remote($word, array(
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
  function link_to_remote($name, $options = array(), $html_options = array())
  {
    return link_to_function($name, remote_function($options), $html_options);
  }

  /**
   * Periodically calls the specified url ('url') every 'frequency' seconds (default is 10).
   * Usually used to update a specified div ('update') with the results of the remote call.
   * The options for specifying the target with 'url' and defining callbacks is the same as 'link_to_remote()'.
   */
  function periodically_call_remote($options = array())
  {
    $frequency = isset($options['frequency']) ? $options['frequency'] : 10; // every ten seconds by default
    $code = 'new PeriodicalExecuter(function() {'.remote_function($options).'}, '.$frequency.')';

    return javascript_tag($code);
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
   *  <?php echo form_remote_tag(array(
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
  function form_remote_tag($options = array(), $options_html = array())
  {
    $options = _parse_attributes($options);
    $options_html = _parse_attributes($options_html);

    $options['form'] = true;

    $options_html['onsubmit'] = remote_function($options).' return false;';
    $options_html['action'] = isset($options_html['action']) ? $options_html['action'] : url_for($options['url']);
    $options_html['method'] = isset($options_html['method']) ? $options_html['method'] : 'post';

    return tag('form', $options_html, true);
  }

  /**
   *  Returns a button input tag that will submit form using XMLHttpRequest in the background instead of regular
   *  reloading POST arrangement. The '$options' argument is the same as in 'form_remote_tag()'.
   */
  function submit_to_remote($name, $value, $options = array(), $options_html = array())
  {
    $options = _parse_attributes($options);
    $options_html = _parse_attributes($options_html);

    if (!isset($options['with']))
    {
      $options['with'] = 'Form.serialize(this.form)';
    }

    $options_html['type'] = 'button';
    $options_html['onclick'] = remote_function($options).' return false;';
    $options_html['name'] = $name;
    $options_html['value'] = $value;

    return tag('input', $options_html, false);
  }

  /**
   *  Returns a image submit tag that will submit form using XMLHttpRequest in the background instead of regular
   *  reloading POST arrangement. The '$options' argument is the same as in 'form_remote_tag()'.
   */
  function submit_image_to_remote($name, $source, $options = array(), $options_html = array())
  {
    $options = _parse_attributes($options);
    $options_html = _parse_attributes($options_html);
 
    if (!isset($options['with']))
    {
      $options['with'] = 'Form.serialize(this.form)';
    }

    $options_html['type'] = 'image';
    $options_html['onclick'] = remote_function($options).' return false;';
    $options_html['name'] = $name;
    $options_html['src'] = image_path($source);

    if (!isset($options_html['alt'])) 
    {
      $path_pos = strrpos($source, '/');
      $dot_pos = strrpos($source, '.');
      $begin = $path_pos ? $path_pos + 1 : 0;
      $nb_str = ($dot_pos ? $dot_pos : strlen($source)) - $begin;
      $options_html['alt'] = ucfirst(substr($source, $begin, $nb_str));
    }
 
    return tag('input', $options_html, false);
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
   *   <?php echo javascript_tag(
   *      update_element_function('products', array(
   *            'position' => 'bottom',
   *            'content'  => "<p>New product!</p>",
   *      ))
   *   ) ?>
   *
   *
   * This method can also be used in combination with remote method call
   * where the result is evaluated afterwards to cause multiple updates on a page.
   *
   * Example:
   *
   *  # Calling view
   *  <?php echo form_remote_tag(array(
   *      'url'      => '@buy',
   *      'complete' => evaluate_remote_response()
   *  )) ?>
   *  all the inputs here...
   *
   *  # Target action
   *  public function executeBuy()
   *  {
   *     $this->product = ProductPeer::retrieveByPk(1);
   *  }
   *
   *  # Returning view
   *  <?php echo update_element_function('cart', array(
   *      'action'   => 'update',
   *      'position' => 'bottom',
   *      'content'  => '<p>New Product: '.$product->getName().'</p>',
   *  )) ?>
   */
  function update_element_function($element_id, $options = array())
  {
    sfContext::getInstance()->getResponse()->addJavascript(sfConfig::get('sf_prototype_web_dir').'/js/prototype');

    $content = escape_javascript(isset($options['content']) ? $options['content'] : '');

    $value = isset($options['action']) ? $options['action'] : 'update';
    switch ($value)
    {
      case 'update':
        if (isset($options['position']) && $options['position'])
        {
          $javascript_function = "\$('$element_id').insert({'".$options['position']."':'$content'})";
        }
        else
        {
          $javascript_function = "\$('$element_id').update('$content')";
        }
        break;

      case 'empty':
        $javascript_function = "\$('$element_id').innerHTML = ''";
        break;

      case 'remove':
        $javascript_function = "\$('$element_id').remove()";
        break;

      default:
        throw new sfException('Invalid action, choose one of update, remove, empty.');
    }

    $javascript_function .= ";\n";

    return (isset($options['binding']) ? $javascript_function.$options['binding'] : $javascript_function);
  }

  /**
   * Returns 'eval(request.responseText)', which is the Javascript function that
   * 'form_remote_tag()' can call in 'complete' to evaluate a multiple update return document
   * using 'update_element_function()' calls.
   */
  function evaluate_remote_response()
  {
    return 'eval(request.responseText)';
  }

  /**
   * Returns the javascript needed for a remote function.
   * Takes the same arguments as 'link_to_remote()'.
   *
   * Example:
   *   <select id="options" onchange="<?php echo remote_function(array('update' => 'options', 'url' => '@update_options')) ?>">
   *     <option value="0">Hello</option>
   *     <option value="1">World</option>
   *   </select>
   */
  function remote_function($options)
  {
    sfContext::getInstance()->getResponse()->addJavascript(sfConfig::get('sf_prototype_web_dir').'/js/prototype');

    $javascript_options = _options_for_ajax($options);

    $update = '';
    if (isset($options['update']) && is_array($options['update']))
    {
      $update = array();
      if (isset($options['update']['success']))
      {
        $update[] = "success:'".$options['update']['success']."'";
      }
      if (isset($options['update']['failure']))
      {
        $update[] = "failure:'".$options['update']['failure']."'";
      }
      $update = '{'.join(',', $update).'}';
    }
    else if (isset($options['update']))
    {
      $update .= "'".$options['update']."'";
    }

    $function = !$update ?  "new Ajax.Request(" : "new Ajax.Updater($update, ";

    $function .= '\''.url_for($options['url']).'\'';
    $function .= ', '.$javascript_options.')';

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
      $function = "if (window.confirm('".escape_javascript($options['confirm'])."')) { $function; }";
      if (isset($options['cancel']))
      {
        $function = $function.' else { '.$options['cancel'].' }';
      }
    }

    return $function.';';
  }

  /**
   * Observes the field with the DOM ID specified by '$field_id' and makes
   * an AJAX call when its contents have changed.
   *
   * Required '$options' are:
   * 'url'                 'url_for()'-style options for the action to call
   *                       when the field has changed.
   *
   * Additional options are:
   * 'frequency'           The frequency (in seconds) at which changes to
   *                       this field will be detected. Not setting this
   *                       option at all or to a value equal to or less than
   *                       zero will use event based observation instead of
   *                       time based observation.
   * 'update'              Specifies the DOM ID of the element whose
   *                       innerHTML should be updated with the
   *                       XMLHttpRequest response text.
   * 'with'                A JavaScript expression specifying the
   *                       parameters for the XMLHttpRequest. This defaults
   *                       to 'value', which in the evaluated context
   *                       refers to the new field value.
   *
   * Additionally, you may specify any of the options documented in
   * link_to_remote().
   */
  function observe_field($field_id, $options = array())
  {
    sfContext::getInstance()->getResponse()->addJavascript(sfConfig::get('sf_prototype_web_dir').'/js/prototype');

    if (isset($options['frequency']) && $options['frequency'] > 0)
    {
      return _build_observer('Form.Element.Observer', $field_id, $options);
    }
    else
    {
      return _build_observer('Form.Element.EventObserver', $field_id, $options);
    }
  }

  /**
   * Like 'observe_field()', but operates on an entire form identified by the
   * DOM ID '$form_id'. '$options' are the same as 'observe_field()', except
   * the default value of the 'with' option evaluates to the
   * serialized (request string) value of the form.
   */
  function observe_form($form_id, $options = array())
  {
    sfContext::getInstance()->getResponse()->addJavascript(sfConfig::get('sf_prototype_web_dir').'/js/prototype');

    if (isset($options['frequency']) && $options['frequency'] > 0)
    {
      return _build_observer('Form.Observer', $form_id, $options);
    }
    else
    {
      return _build_observer('Form.EventObserver', $form_id, $options);
    }
  }

  /**
   * Returns a JavaScript snippet to be used on the AJAX callbacks for starting
   * visual effects.
   *
   * Example:
   *  <?php echo link_to_remote('Reload', array(
   *        'update'  => 'posts',
   *        'url'     => '@reload',
   *        'complete => visual_effect('highlight', 'posts', array('duration' => 0.5 )),
   *  )) ?>
   *
   * If no '$element_id' is given, it assumes "element" which should be a local
   * variable in the generated JavaScript execution context. This can be used
   * for example with drop_receiving_element():
   *
   *  <?php echo drop_receving_element( ..., array(
   *        ...
   *        'loading' => visual_effect('fade'),
   *  )) ?>
   *
   * This would fade the element that was dropped on the drop receiving element.
   *
   * You can change the behaviour with various options, see
   * http://script.aculo.us for more documentation.
   */
  function visual_effect($name, $element_id = false, $js_options = array())
  {
    $response = sfContext::getInstance()->getResponse();
    $response->addJavascript(sfConfig::get('sf_prototype_web_dir').'/js/prototype');
    $response->addJavascript(sfConfig::get('sf_prototype_web_dir').'/js/builder');
    $response->addJavascript(sfConfig::get('sf_prototype_web_dir').'/js/effects');

    $element = $element_id ? "'$element_id'" : 'element';

    if (in_array($name, array('toggle_appear', 'toggle_blind', 'toggle_slide')))
    {
      return "new Effect.toggle($element, '".substr($name, 7)."', ".options_for_javascript($js_options).");";
    }
    else
    {
      return "new Effect.".sfInflector::camelize($name)."($element, ".options_for_javascript($js_options).");";
    }
  }

  /**
   * Makes the elements with the DOM ID specified by '$element_id' sortable
   * by drag-and-drop and if an 'url' is specified make an AJAX call whenever
   * the sort order has changed. By default, the action called gets the 
   * serialized sortable element as parameters.
   *
   * Example:
   *   <?php echo sortable_element($my_list, array(
   *      'url' => '@order',
   *   )) ?>
   *
   * In the example, the action gets a '$my_list' array parameter
   * containing the values of the ids of elements the sortable consists
   * of, in the current order.
   *
   * You can change the behaviour with various options, see
   * http://script.aculo.us for more documentation.
   */
  function sortable_element($element_id, $options = array())
  {
    $response = sfContext::getInstance()->getResponse();
    $response->addJavascript(sfConfig::get('sf_prototype_web_dir').'/js/prototype');
    $response->addJavascript(sfConfig::get('sf_prototype_web_dir').'/js/builder');
    $response->addJavascript(sfConfig::get('sf_prototype_web_dir').'/js/effects');
    $response->addJavascript(sfConfig::get('sf_prototype_web_dir').'/js/dragdrop');

    if (!isset($options['with']))
    {
      $options['with'] = "Sortable.serialize('$element_id')";
    }

    if (!isset($options['onUpdate']) && isset($options['url']))
    {
      $options['onUpdate'] = "function(){".remote_function($options)."}";
    }

    foreach (get_ajax_options() as $key)
    {
      unset($options[$key]);
    }

    foreach (array('tag', 'overlap', 'constraint', 'handle') as $option)
    {
      if (isset($options[$option]))
      {
        $options[$option] = "'{$options[$option]}'";
      }
    }

    if (isset($options['containment']))
    {
      $options['containment'] = array_or_string_for_javascript($options['containment']);
    }

    if (isset($options['hoverclass']))
    {
      $options['hoverclass'] = "'{$options['hoverclass']}'";
    }

    if (isset($options['only']))
    {
      $options['only'] = array_or_string_for_javascript($options['only']);
    }

    $scrollPosition = "";
    if (isset($options['scroll']))
    {
      $options['scroll'] = array_or_string_for_javascript($options['scroll']);
      $scrollPosition = "Position.includeScrollOffsets = true;";
    }

    return javascript_tag($scrollPosition."Sortable.create('$element_id', ".options_for_javascript($options).")");
  }

  /**
   * Makes the element with the DOM ID specified by '$element_id' draggable.
   *
   * Example:
   *   <?php echo draggable_element('my_image', array(
   *      'revert' => true,
   *   )) ?>
   *
   * You can change the behaviour with various options, see
   * http://script.aculo.us for more documentation.
   */
  function draggable_element($element_id, $options = array())
  {
    $response = sfContext::getInstance()->getResponse();
    $response->addJavascript(sfConfig::get('sf_prototype_web_dir').'/js/prototype');
    $response->addJavascript(sfConfig::get('sf_prototype_web_dir').'/js/builder');
    $response->addJavascript(sfConfig::get('sf_prototype_web_dir').'/js/effects');
    $response->addJavascript(sfConfig::get('sf_prototype_web_dir').'/js/dragdrop');

    return javascript_tag("new Draggable('$element_id', ".options_for_javascript($options).")");
  }

  /**
   * Makes the element with the DOM ID specified by '$element_id' receive
   * dropped draggable elements (created by 'draggable_element()') and make an AJAX call.
   * By default, the action called gets the DOM ID of the element as parameter.
   *
   * Example:
   *   <?php drop_receiving_element('my_cart', array(
   *      'url' => 'cart/add',
   *   )) ?>
   *
   * You can change the behaviour with various options, see
   * http://script.aculo.us for more documentation.
   */
  function drop_receiving_element($element_id, $options = array())
  {
    $response = sfContext::getInstance()->getResponse();
    $response->addJavascript(sfConfig::get('sf_prototype_web_dir').'/js/prototype');
    $response->addJavascript(sfConfig::get('sf_prototype_web_dir').'/js/builder');
    $response->addJavascript(sfConfig::get('sf_prototype_web_dir').'/js/effects');
    $response->addJavascript(sfConfig::get('sf_prototype_web_dir').'/js/dragdrop');

    if (!isset($options['with']))
    {
      $options['with'] = "'id=' + encodeURIComponent(element.id)";
    }
    if (!isset($options['onDrop']))
    {
      $options['onDrop'] = "function(element){".remote_function($options)."}";
    }

    foreach (get_ajax_options() as $key)
    {
      unset($options[$key]);
    }

    if (isset($options['accept']))
    {
      $options['accept'] = array_or_string_for_javascript($options['accept']);
    }

    if (isset($options['hoverclass']))
    {
      $options['hoverclass'] = "'{$options['hoverclass']}'";
    }

    return javascript_tag("Droppables.add('$element_id', ".options_for_javascript($options).")");
  }

  /**
   * wrapper for script.aculo.us/prototype Ajax.Autocompleter.
   * @param string name value of input field
   * @param string default value for input field
   * @param array input tag options. (size, autocomplete, etc...)
   * @param array completion options. (use_style, etc...)
   *
   * @return string input field tag, div for completion results, and
   *                 auto complete javascript tags
   */
  function input_auto_complete_tag($name, $value, $url, $tag_options = array(), $completion_options = array())
  {
    $context = sfContext::getInstance();

    $tag_options = _convert_options($tag_options);

    $response = $context->getResponse();
    $response->addJavascript(sfConfig::get('sf_prototype_web_dir').'/js/prototype');
    $response->addJavascript(sfConfig::get('sf_prototype_web_dir').'/js/effects');
    $response->addJavascript(sfConfig::get('sf_prototype_web_dir').'/js/controls');

    $comp_options = _convert_options($completion_options);
    if (isset($comp_options['use_style']) && $comp_options['use_style'] == true)
    {
      $response->addStylesheet(sfConfig::get('sf_prototype_web_dir').'/css/input_auto_complete_tag');
    }

    $tag_options['id'] = get_id_from_name(isset($tag_options['id']) ? $tag_options['id'] : $name);

    $javascript  = tag('input', array_merge(array('type' => 'text', 'name' => $name, 'value' => $value), _convert_options($tag_options)));
    $javascript .= content_tag('div', '' , array('id' => $tag_options['id'].'_auto_complete', 'class' => 'auto_complete'));
    $javascript .= _auto_complete_field($tag_options['id'], $url, $comp_options);

    return $javascript;
  }

  /**
   * wrapper for script.aculo.us/prototype Ajax.InPlaceEditor.
   * @param string name id of field that can be edited
   * @param string url of module/action to be called when ok is clicked
   * @param array editor tag options. (rows, cols, highlightcolor, highlightendcolor, etc...)
   *
   * @return string javascript to manipulate the id field to allow click and edit functionality
   */
  function input_in_place_editor_tag($name, $url, $editor_options = array())
  {
    $response = sfContext::getInstance()->getResponse();
    $response->addJavascript(sfConfig::get('sf_prototype_web_dir').'/js/prototype');
    $response->addJavascript(sfConfig::get('sf_prototype_web_dir').'/js/effects');
    $response->addJavascript(sfConfig::get('sf_prototype_web_dir').'/js/controls');

    $editor_options = _convert_options($editor_options);
    return _in_place_editor($name, $url, $editor_options);
  }

  /*
   * Makes an HTML element specified by the DOM ID '$field_id' become an in-place
   * editor of a property.
   *
   * A form is automatically created and displayed when the user clicks the element,
   * something like this:
   * <form id="myElement-in-place-edit-form" target="specified url">
   *   <input name="value" text="The content of myElement"/>
   *   <input type="submit" value="ok"/>
   *   <a onclick="javascript to cancel the editing">cancel</a>
   * </form>
   *
   * The form is serialized and sent to the server using an AJAX call, the action on
   * the server should process the value and return the updated value in the body of
   * the reponse. The element will automatically be updated with the changed value
   * (as returned from the server).
   *
   * Required '$options' are:
   * 'url'                 Specifies the url where the updated value should
   *                       be sent after the user presses "ok".
   *
   * Some additional '$options' are:
   * 'rows'                Number of rows (more than 1 will use a TEXTAREA)
   * 'cancel_text'         The text on the cancel link. (default: "cancel")
   * 'save_text'           The text on the save link. (default: "ok")
   * 'external_control'    The id of an external control used to enter edit mode.
   * 'options'             Pass through options to the AJAX call (see prototype's Ajax.Updater)
   * 'with'                JavaScript snippet that should return what is to be sent
   *                       in the AJAX call, 'form' is an implicit parameter
   *
   * for details see: http://mir.aculo.us/2007/7/17/in-place-editing-the-summer-2007-rewrite/
   */
    function _in_place_editor($field_id, $url, $options = array())
    {
      $javascript = "new Ajax.InPlaceEditor(";

      $javascript .= "'$field_id', ";
      $javascript .= array_or_string_for_javascript(url_for($url));

      // translate symfony option names to InPlaceEditor options
      if (isset($options['cancel_text']))
      {
        $options['cancelText'] = array_or_string_for_javascript($options['cancel_text']);
        unset($options['cancel_text']);
      }
      if (isset($options['save_text']))
      {
        $options['okText'] = array_or_string_for_javascript($options['save_text']);
        unset($options['save_text']);
      }
      if (isset($options['external_control']))
      {
        $options['externalControl'] = array_or_string_for_javascript($options['external_control']);
        unset($options['external_control']);
      }
      if (isset($options['options']))
      {
        $options['ajaxOptions'] = $options['options'];
        unset($options['options']);
      }
      if (isset($options['with']))
      {
        $options['callback'] = "function(form, value) { return ".$options['with']." }";
        unset($options['with']);
      }
      if (isset($options['highlightcolor']))
      {
        $options['highlightColor'] = array_or_string_for_javascript($options['highlightcolor']);
        unset($options['highlightcolor']);
      }
      if (isset($options['highlightendcolor']))
      {
        $options['highlightEndColor'] = array_or_string_for_javascript($options['highlightendcolor']);
        unset($options['highlightendcolor']);
      }
      if (isset($options['loadTextURL']))
      {
        $options['loadTextURL'] =  array_or_string_for_javascript($options['loadTextURL']);
      }

      $javascript .= ', '.options_for_javascript($options);
      $javascript .= ');';

      return javascript_tag($javascript);
    }

  /**
   * wrapper for script.aculo.us/prototype Ajax.Autocompleter.
   * @param string id value of input field
   * @param string url of module/action to execute for autocompletion
   * @param array completion options
   * @return string javascript tag for Ajax.Autocompleter
   */
  function _auto_complete_field($field_id, $url, $options = array())
  {
    $javascript = "new Ajax.Autocompleter(";

    $javascript .= "'".get_id_from_name($field_id)."', ";
    if (isset($options['update']))
    {
      $javascript .= "'".$options['update']."', ";
    }
    else
    {
      $javascript .= "'".get_id_from_name($field_id)."_auto_complete', ";
    }

    $javascript .= array_or_string_for_javascript(url_for($url));

    $js_options = array();
    if (isset($options['tokens']))
    {
      $js_options['tokens'] = array_or_string_for_javascript($options['tokens']);
    }
    if (isset ($options['with']))
    {
      $js_options['callback'] = "function(element, value) { return ".$options['with']."}";
    }
    if (isset($options['indicator']))
    {
      $js_options['indicator']  = array_or_string_for_javascript($options['indicator']);
    }
    if (isset($options['on_show']))
    {
      $js_options['onShow'] = $options['on_show'];
    }
    if (isset($options['on_hide']))
    {
      $js_options['onHide'] = $options['on_hide'];
    }
    if (isset($options['min_chars']))
    {
      $js_options['minChars'] = $options['min_chars'];
    }
    if (isset($options['frequency']))
    {
      $js_options['frequency'] = $options['frequency'];
    }
    if (isset($options['update_element']))
    {
      $js_options['updateElement'] = $options['update_element'];
    }
    if (isset($options['after_update_element']))
    {
      $js_options['afterUpdateElement'] = $options['after_update_element'];
    }
    if (isset($options['param_name'])) 
    {
      $js_options['paramName'] = "'".$options['param_name']."'";
    }

    $javascript .= ', '.options_for_javascript($js_options).');';

    return javascript_tag($javascript);
  }

  function _options_for_ajax($options)
  {
    $js_options = _build_callbacks($options);

    $js_options['asynchronous'] = (isset($options['type']) && ($options['type'] == 'synchronous')) ? false : true;
    if (isset($options['method'])) $js_options['method'] = array_or_string_for_javascript($options['method']);
    if (isset($options['position'])) $js_options['insertion'] = "Insertion.".sfInflector::camelize($options['position']);
    $js_options['evalScripts'] = (!isset($options['script']) || $options['script'] == '0' || $options['script'] == false) ? false : true;

    if (isset($options['form']))
    {
      $js_options['parameters'] = 'Form.serialize(this)';
    }
    else if (isset($options['submit']))
    {
      $js_options['parameters'] = "Form.serialize(document.getElementById('{$options['submit']}'))";
    }
    else if (isset($options['with']))
    {
      $js_options['parameters'] = $options['with'];
    }

    return options_for_javascript($js_options);
  }

  function _build_observer($klass, $name, $options = array())
  {
    if (!isset($options['with']) && isset($options['update']))
    {
      $options['with'] = 'value';
    }

    $callback = remote_function($options);

    $javascript  = 'new '.$klass.'("'.$name.'", ';
    if (isset($options['frequency']) && $options['frequency'] > 0)
    {
      $javascript .= $options['frequency'].", ";
    }
    $javascript .= "function(element, value) {";
    $javascript .= $callback.'});';

    return javascript_tag($javascript);
  }

  function _build_callbacks($options)
  {
    $callbacks = array();
    foreach (get_callbacks() as $callback)
    {
      if (isset($options[$callback]))
      {
        $name = 'on'.ucfirst($callback);
        $code = $options[$callback];
        $callbacks[$name] = 'function(request, json){'.$code.'}';
      }
    }

    return $callbacks;
  }
