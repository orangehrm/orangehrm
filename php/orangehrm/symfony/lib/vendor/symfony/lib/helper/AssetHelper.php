<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) 2004 David Heinemeier Hansson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * AssetHelper.
 *
 * @package    symfony
 * @subpackage helper
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     David Heinemeier Hansson
 * @version    SVN: $Id: AssetHelper.php 17858 2009-05-01 21:22:50Z FabianLange $
 */

/**
 * Returns a <link> tag that browsers and news readers
 * can use to auto-detect a RSS or ATOM feed for the current page,
 * to be included in the <head> section of a HTML document.
 *
 * <b>Options:</b>
 * - rel - defaults to 'alternate'
 * - type - defaults to 'application/rss+xml'
 * - title - defaults to the feed type in upper case
 *
 * <b>Examples:</b>
 * <code>
 *  echo auto_discovery_link_tag('rss', 'module/feed');
 *    => <link rel="alternate" type="application/rss+xml" title="RSS" href="http://www.curenthost.com/module/feed" />
 *  echo auto_discovery_link_tag('rss', 'module/feed', array('title' => 'My RSS'));
 *    => <link rel="alternate" type="application/rss+xml" title="My RSS" href="http://www.curenthost.com/module/feed" />
 * </code>
 *
 * @param string $type        feed type ('rss', 'atom')
 * @param string $url         'module/action' or '@rule' of the feed
 * @param array  $tag_options additional HTML compliant <link> tag parameters
 *
 * @return string XHTML compliant <link> tag
 */
function auto_discovery_link_tag($type = 'rss', $url = '', $tag_options = array())
{
  return tag('link', array(
    'rel'   => isset($tag_options['rel']) ? $tag_options['rel'] : 'alternate',
    'type'  => isset($tag_options['type']) ? $tag_options['type'] : 'application/'.$type.'+xml',
    'title' => isset($tag_options['title']) ? $tag_options['title'] : ucfirst($type),
    'href'  => url_for($url, true)
  ));
}

/**
 * Returns the path to a JavaScript asset.
 *
 * <b>Example:</b>
 * <code>
 *  echo javascript_path('myscript');
 *    => /js/myscript.js
 * </code>
 *
 * <b>Note:</b> The asset name can be supplied as a...
 * - full path, like "/my_js/myscript.css"
 * - file name, like "myscript.js", that gets expanded to "/js/myscript.js"
 * - file name without extension, like "myscript", that gets expanded to "/js/myscript.js"
 *
 * @param string $source   asset name
 * @param bool   $absolute return absolute path ?
 *
 * @return string file path to the JavaScript file
 * @see    javascript_include_tag
 */
function javascript_path($source, $absolute = false)
{
  return _compute_public_path($source, 'js', 'js', $absolute);
}

/**
 * Returns a <script> include tag per source given as argument.
 *
 * <b>Examples:</b>
 * <code>
 *  echo javascript_include_tag('xmlhr');
 *    => <script language="JavaScript" type="text/javascript" src="/js/xmlhr.js"></script>
 *  echo javascript_include_tag('common.javascript', '/elsewhere/cools');
 *    => <script language="JavaScript" type="text/javascript" src="/js/common.javascript"></script>
 *       <script language="JavaScript" type="text/javascript" src="/elsewhere/cools.js"></script>
 * </code>
 *
 * @param string asset names
 * @param array additional HTML compliant <link> tag parameters
 *
 * @return string XHTML compliant <script> tag(s)
 * @see    javascript_path
 */
function javascript_include_tag()
{
  $sources = func_get_args();
  $sourceOptions = (func_num_args() > 1 && is_array($sources[func_num_args() - 1])) ? array_pop($sources) : array();

  $html = '';
  foreach ($sources as $source)
  {
    $absolute = false;
    if (isset($sourceOptions['absolute']))
    {
      unset($sourceOptions['absolute']);
      $absolute = true;
    }

    $condition = null;
    if (isset($sourceOptions['condition']))
    {
      $condition = $sourceOptions['condition'];
      unset($sourceOptions['condition']);
    }

    if (!isset($sourceOptions['raw_name']))
    {
      $source = javascript_path($source, $absolute);
    }
    else
    {
      unset($sourceOptions['raw_name']);
    }

    $options = array_merge(array('type' => 'text/javascript', 'src' => $source), $sourceOptions);
    $tag = content_tag('script', '', $options);

    if (!is_null($condition))
    {
      $tag = comment_as_conditional($condition, $tag);
    }

    $html .= $tag."\n";
  }

  return $html;
}

/**
 * Returns the path to a stylesheet asset.
 *
 * <b>Example:</b>
 * <code>
 *  echo stylesheet_path('style');
 *    => /css/style.css
 * </code>
 *
 * <b>Note:</b> The asset name can be supplied as a...
 * - full path, like "/my_css/style.css"
 * - file name, like "style.css", that gets expanded to "/css/style.css"
 * - file name without extension, like "style", that gets expanded to "/css/style.css"
 *
 * @param string $source   asset name
 * @param bool   $absolute return absolute path ?
 *
 * @return string file path to the stylesheet file
 * @see    stylesheet_tag
 */
function stylesheet_path($source, $absolute = false)
{
  return _compute_public_path($source, 'css', 'css', $absolute);
}

/**
 * Returns a css <link> tag per source given as argument,
 * to be included in the <head> section of a HTML document.
 *
 * <b>Options:</b>
 * - rel - defaults to 'stylesheet'
 * - type - defaults to 'text/css'
 * - media - defaults to 'screen'
 *
 * <b>Examples:</b>
 * <code>
 *  echo stylesheet_tag('style');
 *    => <link href="/stylesheets/style.css" media="screen" rel="stylesheet" type="text/css" />
 *  echo stylesheet_tag('style', array('media' => 'all'));
 *    => <link href="/stylesheets/style.css" media="all" rel="stylesheet" type="text/css" />
 *  echo stylesheet_tag('style', array('raw_name' => true));
 *    => <link href="style" media="all" rel="stylesheet" type="text/css" />
 *  echo stylesheet_tag('random.styles', '/css/stylish');
 *    => <link href="/stylesheets/random.styles" media="screen" rel="stylesheet" type="text/css" />
 *       <link href="/css/stylish.css" media="screen" rel="stylesheet" type="text/css" />
 * </code>
 *
 * @param string asset names
 * @param array  additional HTML compliant <link> tag parameters
 *
 * @return string XHTML compliant <link> tag(s)
 * @see    stylesheet_path
 */
function stylesheet_tag()
{
  $sources = func_get_args();
  $sourceOptions = (func_num_args() > 1 && is_array($sources[func_num_args() - 1])) ? array_pop($sources) : array();

  $html = '';
  foreach ($sources as $source)
  {
    $absolute = false;
    if (isset($sourceOptions['absolute']))
    {
      unset($sourceOptions['absolute']);
      $absolute = true;
    }

    $condition = null;
    if (isset($sourceOptions['condition']))
    {
      $condition = $sourceOptions['condition'];
      unset($sourceOptions['condition']);
    }

    if (!isset($sourceOptions['raw_name']))
    {
      $source = stylesheet_path($source, $absolute);
    }
    else
    {
      unset($sourceOptions['raw_name']);
    }

    $options = array_merge(array('rel' => 'stylesheet', 'type' => 'text/css', 'media' => 'screen', 'href' => $source), $sourceOptions);
    $tag = tag('link', $options);

    if (!is_null($condition))
    {
      $tag = comment_as_conditional($condition, $tag);
    }

    $html .= $tag."\n";
  }

  return $html;
}

/**
 * Adds a stylesheet to the response object.
 *
 * @see sfResponse->addStylesheet()
 */
function use_stylesheet($css, $position = '', $options = array())
{
  sfContext::getInstance()->getResponse()->addStylesheet($css, $position, $options);
}

/**
 * Adds a javascript to the response object.
 *
 * @see sfResponse->addJavascript()
 */
function use_javascript($js, $position = '', $options = array())
{
  sfContext::getInstance()->getResponse()->addJavascript($js, $position, $options);
}

/**
 * Decorates the current template with a given layout.
 *
 * @param mixed $layout The layout name or path or false to disable the layout
 */
function decorate_with($layout)
{
  if (false === $layout)
  {
    sfContext::getInstance()->get('view_instance')->setDecorator(false);
  }
  else
  {
    sfContext::getInstance()->get('view_instance')->setDecoratorTemplate($layout);
  }
}

/**
 * Returns the path to an image asset.
 *
 * <b>Example:</b>
 * <code>
 *  echo image_path('foobar');
 *    => /images/foobar.png
 * </code>
 *
 * <b>Note:</b> The asset name can be supplied as a...
 * - full path, like "/my_images/image.gif"
 * - file name, like "rss.gif", that gets expanded to "/images/rss.gif"
 * - file name without extension, like "logo", that gets expanded to "/images/logo.png"
 *
 * @param string $source   asset name
 * @param bool   $absolute return absolute path ?
 *
 * @return string file path to the image file
 * @see    image_tag
 */
function image_path($source, $absolute = false)
{
  return _compute_public_path($source, 'images', 'png', $absolute);
}

/**
 * Returns an <img> image tag for the asset given as argument.
 *
 * <b>Options:</b>
 * - 'absolute' - to output absolute file paths, useful for embedded images in emails
 * - 'alt'  - defaults to the file name part of the asset (capitalized and without the extension)
 * - 'size' - Supplied as "XxY", so "30x45" becomes width="30" and height="45"
 *
 * <b>Examples:</b>
 * <code>
 *  echo image_tag('foobar');
 *    => <img src="images/foobar.png" alt="Foobar" />
 *  echo image_tag('/my_images/image.gif', array('alt' => 'Alternative text', 'size' => '100x200'));
 *    => <img src="/my_images/image.gif" alt="Alternative text" width="100" height="200" />
 * </code>
 *
 * @param string $source  image asset name
 * @param array  $options additional HTML compliant <img> tag parameters
 *
 * @return string XHTML compliant <img> tag
 * @see    image_path
 */
function image_tag($source, $options = array())
{
  if (!$source)
  {
    return '';
  }

  $options = _parse_attributes($options);

  $absolute = false;
  if (isset($options['absolute']))
  {
    unset($options['absolute']);
    $absolute = true;
  }

  if (!isset($options['raw_name']))
  {
    $options['src'] = image_path($source, $absolute);
  }
  else
  {
    $options['src'] = $source;
    unset($options['raw_name']);
  }

  if (isset($options['alt_title']))
  {
    // set as alt and title but do not overwrite explicitly set
    if (!isset($options['alt']))
    {
      $options['alt'] = $options['alt_title'];
    }
    if (!isset($options['title']))
    {
      $options['title'] = $options['alt_title'];
    }
    unset($options['alt_title']);
  }

  if (!isset($options['alt']) && sfConfig::get('sf_compat_10'))
  {
    $path_pos = strrpos($source, '/');
    $dot_pos = strrpos($source, '.');
    $begin = $path_pos ? $path_pos + 1 : 0;
    $nb_str = ($dot_pos ? $dot_pos : strlen($source)) - $begin;
    $options['alt'] = ucfirst(substr($source, $begin, $nb_str));
  }

  if (isset($options['size']))
  {
    list($options['width'], $options['height']) = explode('x', $options['size'], 2);
    unset($options['size']);
  }

  return tag('img', $options);
}

function _compute_public_path($source, $dir, $ext, $absolute = false)
{
  if (strpos($source, '://'))
  {
    return $source;
  }

  $request = sfContext::getInstance()->getRequest();
  $sf_relative_url_root = $request->getRelativeUrlRoot();
  if (0 !== strpos($source, '/'))
  {
    $source = $sf_relative_url_root.'/'.$dir.'/'.$source;
  }

  $query_string = '';
  if (false !== $pos = strpos($source, '?'))
  {
    $query_string = substr($source, $pos);
    $source = substr($source, 0, $pos);
  }

  if (false === strpos(basename($source), '.'))
  {
    $source .= '.'.$ext;
  }

  if ($sf_relative_url_root && 0 !== strpos($source, $sf_relative_url_root))
  {
    $source = $sf_relative_url_root.$source;
  }

  if ($absolute)
  {
    $source = 'http'.($request->isSecure() ? 's' : '').'://'.$request->getHost().$source;
  }

  return $source.$query_string;
}

/**
 * Prints a set of <meta> tags according to the response attributes,
 * to be included in the <head> section of a HTML document.
 *
 * <b>Examples:</b>
 * <code>
 *  include_metas();
 *    => <meta name="title" content="symfony - open-source PHP5 web framework" />
 *       <meta name="robots" content="index, follow" />
 *       <meta name="description" content="symfony - open-source PHP5 web framework" />
 *       <meta name="keywords" content="symfony, project, framework, php, php5, open-source, mit, symphony" />
 *       <meta name="language" content="en" /><link href="/stylesheets/style.css" media="screen" rel="stylesheet" type="text/css" />
 * </code>
 *
 * <b>Note:</b> Modify the view.yml or use sfWebResponse::addMeta() to change, add or remove metas.
 *
 * @return string XHTML compliant <meta> tag(s)
 * @see    include_http_metas
 * @see    sfWebResponse::addMeta()
 */
function include_metas()
{
  $context = sfContext::getInstance();
  $i18n = sfConfig::get('sf_i18n') ? $context->getI18N() : null;
  foreach ($context->getResponse()->getMetas() as $name => $content)
  {
    echo tag('meta', array('name' => $name, 'content' => is_null($i18n) ? $content : $i18n->__($content)))."\n";
  }
}

/**
 * Returns a set of <meta http-equiv> tags according to the response attributes,
 * to be included in the <head> section of a HTML document.
 *
 * <b>Examples:</b>
 * <code>
 *  include_http_metas();
 *    => <meta http-equiv="content-type" content="text/html; charset=utf-8" />
 * </code>
 *
 * <b>Note:</b> Modify the view.yml or use sfWebResponse::addHttpMeta() to change, add or remove HTTP metas.
 *
 * @return string XHTML compliant <meta> tag(s)
 * @see    include_metas
 * @see    sfWebResponse::addHttpMeta()
 */
function include_http_metas()
{
  foreach (sfContext::getInstance()->getResponse()->getHttpMetas() as $httpequiv => $value)
  {
    echo tag('meta', array('http-equiv' => $httpequiv, 'content' => $value))."\n";
  }
}

/**
 * Returns the title of the current page according to the response attributes,
 * to be included in the <title> section of a HTML document.
 *
 * <b>Note:</b> Modify the sfResponse object or the view.yml to modify the title of a page.
 *
 * @return string page title
 */
function include_title()
{
  $title = sfContext::getInstance()->getResponse()->getTitle();

  echo content_tag('title', $title)."\n";
}

/**
 * Returns <script> tags for all javascripts configured in view.yml or added to the response object.
 *
 * You can use this helper to decide the location of javascripts in pages.
 * By default, if you don't call this helper, symfony will automatically include javascripts before </head>.
 * Calling this helper disables this behavior.
 *
 * @return string <script> tags
 */
function get_javascripts()
{
  $response = sfContext::getInstance()->getResponse();
  sfConfig::set('symfony.asset.javascripts_included', true);

  $html = '';
  foreach ($response->getJavascripts() as $file => $options)
  {
    $html .= javascript_include_tag($file, $options);
  }

  return $html;
}

/**
 * Prints <script> tags for all javascripts configured in view.yml or added to the response object.
 *
 * @see get_javascripts()
 */
function include_javascripts()
{
  echo get_javascripts();
}

/**
 * Returns <link> tags for all stylesheets configured in view.yml or added to the response object.
 *
 * You can use this helper to decide the location of stylesheets in pages.
 * By default, if you don't call this helper, symfony will automatically include stylesheets before </head>.
 * Calling this helper disables this behavior.
 *
 * @return string <link> tags
 */
function get_stylesheets()
{
  $response = sfContext::getInstance()->getResponse();
  sfConfig::set('symfony.asset.stylesheets_included', true);

  $html = '';
  foreach ($response->getStylesheets() as $file => $options)
  {
    $html .= stylesheet_tag($file, $options);
  }

  return $html;
}

/**
 * Prints <link> tags for all stylesheets configured in view.yml or added to the response object.
 *
 * @see get_stylesheets()
 */
function include_stylesheets()
{
  echo get_stylesheets();
}

/**
 * Returns a <script> include tag for the given internal URI.
 *
 * The helper automatically adds the sf_format to the internal URI, so you don't have to.
 *
 * @param string $uri      The internal URI for the dynamic javascript
 * @param bool   $absolute Whether to generate an absolute URL
 * @param array  $options  An array of options
 *
 * @return string XHTML compliant <script> tag(s)
 * @see    javascript_include_tag
 */
function dynamic_javascript_include_tag($uri, $absolute = false, $options = array())
{
  $options['raw_name'] = true;

  return javascript_include_tag(_dynamic_path($uri, 'js', $absolute), $options);
}

/**
 * Adds a dynamic javascript to the response object.
 *
 * The first argument is an internal URI.
 * The helper automatically adds the sf_format to the internal URI, so you don't have to.
 *
 * @see sfResponse->addJavascript()
 */
function use_dynamic_javascript($js, $position = '', $options = array())
{
  $options['raw_name'] = true;

  return use_javascript(_dynamic_path($js, 'js'), $position, $options);
}

/**
 * Adds a dynamic stylesheet to the response object.
 *
 * The first argument is an internal URI.
 * The helper automatically adds the sf_format to the internal URI, so you don't have to.
 *
 * @see sfResponse->addStylesheet()
 */
function use_dynamic_stylesheet($css, $position = '', $options = array())
{
  $options['raw_name'] = true;

  return use_stylesheet(_dynamic_path($css, 'css'), $position, $options);
}

function _dynamic_path($uri, $format, $absolute = false)
{
  return url_for($uri.(false === strpos($uri, '?') ? '?' : '&').'sf_format='.$format, $absolute);
}

/**
 * Returns <script> tags for all javascripts associated with the given form.
 *
 * @return string <script> tags
 */
function get_javascripts_for_form(sfForm $form)
{
  $html = '';
  foreach ($form->getJavascripts() as $file)
  {
    $html .= javascript_include_tag($file);
  }

  return $html;
}

/**
 * Prints <script> tags for all javascripts associated with the given form.
 *
 * @see get_javascripts_for_form()
 */
function include_javascripts_for_form(sfForm $form)
{
  echo get_javascripts_for_form($form);
}

/**
 * Returns <link> tags for all stylesheets associated with the given form.
 *
 * @return string <link> tags
 */
function get_stylesheets_for_form(sfForm $form)
{
  $html = '';
  foreach ($form->getStylesheets() as $file => $media)
  {
    $html .= stylesheet_tag($file, array('media' => $media));
  }

  return $html;
}

/**
 * Prints <link> tags for all stylesheets associated with the given form.
 *
 * @see get_stylesheets_for_form()
 */
function include_stylesheets_for_form(sfForm $form)
{
  echo get_stylesheets_for_form($form);
}
