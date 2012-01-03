<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWebDebug creates debug information for easy debugging in the browser.
 *
 * @package    symfony
 * @subpackage debug
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfWebDebug.class.php 32890 2011-08-05 07:44:44Z fabien $
 */
class sfWebDebug
{
  protected
    $dispatcher = null,
    $logger     = null,
    $options    = array(),
    $panels     = array();

  /**
   * Constructor.
   *
   * Available options:
   *
   *  * image_root_path:    The image root path
   *  * request_parameters: The current request parameters
   *
   * @param sfEventDispatcher $dispatcher The event dispatcher
   * @param sfVarLogger       $logger     The logger
   * @param array             $options    An array of options
   */
  public function __construct(sfEventDispatcher $dispatcher, sfVarLogger $logger, array $options = array())
  {
    $this->dispatcher = $dispatcher;
    $this->logger     = $logger;
    $this->options    = $options;

    if (!isset($this->options['image_root_path']))
    {
      $this->options['image_root_path'] = '';
    }

    if (!isset($this->options['request_parameters']))
    {
      $this->options['request_parameters'] = array();
    }

    $this->configure();

    $this->dispatcher->notify(new sfEvent($this, 'debug.web.load_panels'));
  }

  /**
   * Configures the web debug toolbar.
   */
  public function configure()
  {
    $this->setPanel('symfony_version', new sfWebDebugPanelSymfonyVersion($this));
    if (sfConfig::get('sf_debug') && sfConfig::get('sf_cache'))
    {
      $this->setPanel('cache', new sfWebDebugPanelCache($this));
    }
    if (sfConfig::get('sf_logging_enabled'))
    {
      $this->setPanel('config', new sfWebDebugPanelConfig($this));
      $this->setPanel('view', new sfWebDebugPanelView($this));
    }
    $this->setPanel('logs', new sfWebDebugPanelLogs($this));
    $this->setPanel('memory', new sfWebDebugPanelMemory($this));
    if (sfConfig::get('sf_debug'))
    {
      $this->setPanel('time', new sfWebDebugPanelTimer($this));
    }

    $this->setPanel('mailer', new sfWebDebugPanelMailer($this));
  }

  /**
   * Gets the logger.
   *
   * @return sfVarLogger The logger instance
   */
  public function getLogger()
  {
    return $this->logger;
  }

  /**
   * Gets the event dispatcher.
   *
   * @return sfEventDispatcher The event dispatcher
   */
  public function getEventDispatcher()
  {
    return $this->dispatcher;
  }

  /**
   * Gets the registered panels.
   *
   * @return array The panels
   */
  public function getPanels()
  {
    return $this->panels;
  }

  /**
   * Sets a panel by name.
   *
   * @param string          $name  The panel name
   * @param sfWebDebugPanel $panel The panel
   */
  public function setPanel($name, sfWebDebugPanel $panel)
  {
    $this->panels[$name] = $panel;
  }

  /**
   * Removes a panel by name.
   *
   * @param string $name The panel name
   */
  public function removePanel($name)
  {
    unset($this->panels[$name]);
  }

  /**
   * Gets an option value by name.
   *
   * @param string $name The option name
   *
   * @return mixed The option value
   */
  public function getOption($name, $default = null)
  {
    return isset($this->options[$name]) ? $this->options[$name] : $default;
  }

  /**
   * Injects the web debug toolbar into a given HTML string.
   *
   * @param string $content The HTML content
   *
   * @return string The content with the web debug toolbar injected
   */
  public function injectToolbar($content)
  {
    if (function_exists('mb_stripos'))
    {
      $posFunction = 'mb_stripos';
      $posrFunction = 'mb_strripos';
      $substrFunction = 'mb_substr';
    }
    else
    {
      $posFunction = 'stripos';
      $posrFunction = 'strripos';
      $substrFunction = 'substr';
    }

    if (false !== $pos = $posFunction($content, '</head>'))
    {
      $styles = '<style type="text/css">'.str_replace(array("\r", "\n"), ' ', $this->getStylesheet()).'</style>';
      $content = $substrFunction($content, 0, $pos).$styles.$substrFunction($content, $pos);
    }

    $debug = $this->asHtml();
    if (false === $pos = $posrFunction($content, '</body>'))
    {
      $content .= $debug;
    }
    else
    {
      $content = $substrFunction($content, 0, $pos).'<script type="text/javascript">'.$this->getJavascript().'</script>'.$debug.$substrFunction($content, $pos);
    }

    return $content;
  }

  /**
   * Returns the web debug toolbar as HTML.
   *
   * @return string The web debug toolbar HTML
   */
  public function asHtml()
  {
    $current = isset($this->options['request_parameters']['sfWebDebugPanel']) ? $this->options['request_parameters']['sfWebDebugPanel'] : null;

    $titles = array();
    $panels = array();
    foreach ($this->panels as $name => $panel)
    {
      if ($title = $panel->getTitle())
      {
        if (($content = $panel->getPanelContent()) || $panel->getTitleUrl())
        {
          $id = sprintf('sfWebDebug%sDetails', $name);
          $titles[] = sprintf('<li%s><a title="%s" href="%s"%s>%s</a></li>',
            $panel->getStatus() ? ' class="sfWebDebug'.ucfirst($this->getPriority($panel->getStatus())).'"' : '',
            $panel->getPanelTitle(),
            $panel->getTitleUrl() ? $panel->getTitleUrl() : '#',
            $panel->getTitleUrl() ? '' : ' onclick="sfWebDebugShowDetailsFor(\''.$id.'\'); return false;"',
            $title
          );
          $panels[] = sprintf('<div id="%s" class="sfWebDebugTop" style="display:%s"><h1>%s</h1>%s</div>',
            $id,
            $name == $current ? 'block' : 'none',
            $panel->getPanelTitle(),
            $content
          );
        }
        else
        {
          $titles[] = sprintf('<li>%s</li>', $title);
        }
      }
    }

    return '
      <div id="sfWebDebug">
        <div id="sfWebDebugBar">
          <a href="#" onclick="sfWebDebugToggleMenu(); return false;"><img src="'.$this->options['image_root_path'].'/sf.png" alt="Debug toolbar" /></a>

          <ul id="sfWebDebugDetails" class="sfWebDebugMenu">
            '.implode("\n", $titles).'
            <li class="last">
              <a href="#" onclick="document.getElementById(\'sfWebDebug\').style.display=\'none\'; return false;"><img src="'.$this->options['image_root_path'].'/close.png" alt="Close" /></a>
            </li>
          </ul>
        </div>

        '.implode("\n", $panels).'
      </div>
    ';
  }

  /**
   * Converts a priority value to a string.
   *
   * @param integer $value The priority value
   *
   * @return string The priority as a string
   */
  public function getPriority($value)
  {
    if ($value >= sfLogger::INFO)
    {
      return 'info';
    }
    else if ($value >= sfLogger::WARNING)
    {
      return 'warning';
    }
    else
    {
      return 'error';
    }
  }

  /**
   * Gets the javascript code to inject in the head tag.
   *
   * @param string The javascript code
   */
  public function getJavascript()
  {
    return <<<EOF
/* <![CDATA[ */
function sfWebDebugGetElementsByClassName(strClass, strTag, objContElm)
{
  // http://muffinresearch.co.uk/archives/2006/04/29/getelementsbyclassname-deluxe-edition/
  strTag = strTag || "*";
  objContElm = objContElm || document;
  var objColl = (strTag == '*' && document.all) ? document.all : objContElm.getElementsByTagName(strTag);
  var arr = new Array();
  var delim = strClass.indexOf('|') != -1  ? '|' : ' ';
  var arrClass = strClass.split(delim);
  var j = objColl.length;
  for (var i = 0; i < j; i++) {
    if(objColl[i].className == undefined) continue;
    var arrObjClass = objColl[i].className.split ? objColl[i].className.split(' ') : [];
    if (delim == ' ' && arrClass.length > arrObjClass.length) continue;
    var c = 0;
    comparisonLoop:
    {
      var l = arrObjClass.length;
      for (var k = 0; k < l; k++) {
        var n = arrClass.length;
        for (var m = 0; m < n; m++) {
          if (arrClass[m] == arrObjClass[k]) c++;
          if (( delim == '|' && c == 1) || (delim == ' ' && c == arrClass.length)) {
            arr.push(objColl[i]);
            break comparisonLoop;
          }
        }
      }
    }
  }
  return arr;
}

function sfWebDebugToggleMenu()
{
  var element = document.getElementById('sfWebDebugDetails');

  var cacheElements = sfWebDebugGetElementsByClassName('sfWebDebugCache');
  var mainCacheElements = sfWebDebugGetElementsByClassName('sfWebDebugActionCache');
  var panelElements = sfWebDebugGetElementsByClassName('sfWebDebugTop');

  if (element.style.display != 'none')
  {
    for (var i = 0; i < panelElements.length; ++i)
    {
      panelElements[i].style.display = 'none';
    }

    // hide all cache information
    for (var i = 0; i < cacheElements.length; ++i)
    {
      cacheElements[i].style.display = 'none';
    }
    for (var i = 0; i < mainCacheElements.length; ++i)
    {
      mainCacheElements[i].style.border = 'none';
    }
  }
  else
  {
    for (var i = 0; i < cacheElements.length; ++i)
    {
      cacheElements[i].style.display = '';
    }
    for (var i = 0; i < mainCacheElements.length; ++i)
    {
      mainCacheElements[i].style.border = '1px solid #f00';
    }
  }

  sfWebDebugToggle('sfWebDebugDetails');
  sfWebDebugToggle('sfWebDebugShowMenu');
  sfWebDebugToggle('sfWebDebugHideMenu');
}

function sfWebDebugShowDetailsFor(element)
{
  if (typeof element == 'string')
    element = document.getElementById(element);

  var panelElements = sfWebDebugGetElementsByClassName('sfWebDebugTop');
  for (var i = 0; i < panelElements.length; ++i)
  {
    if (panelElements[i] != element)
    {
      panelElements[i].style.display = 'none';
    }
  }

  sfWebDebugToggle(element);
}

function sfWebDebugToggle(element)
{
  if (typeof element == 'string')
    element = document.getElementById(element);

  if (element)
    element.style.display = element.style.display == 'none' ? '' : 'none';
}

function sfWebDebugToggleMessages(klass)
{
  var elements = sfWebDebugGetElementsByClassName(klass);

  var x = elements.length;
  for (var i = 0; i < x; ++i)
  {
    sfWebDebugToggle(elements[i]);
  }
}

function sfWebDebugToggleAllLogLines(show, klass)
{
  var elements = sfWebDebugGetElementsByClassName(klass);
  var x = elements.length;
  for (var i = 0; i < x; ++i)
  {
    elements[i].style.display = show ? '' : 'none';
  }
}

function sfWebDebugShowOnlyLogLines(type)
{
  var types = new Array();
  types[0] = 'info';
  types[1] = 'warning';
  types[2] = 'error';
  for (klass in types)
  {
    var elements = sfWebDebugGetElementsByClassName('sfWebDebug' + types[klass].substring(0, 1).toUpperCase() + types[klass].substring(1, types[klass].length));
    var x = elements.length;
    for (var i = 0; i < x; ++i)
    {
      if ('tr' == elements[i].tagName.toLowerCase())
      {
        elements[i].style.display = (type == types[klass]) ? '' : 'none';
      }
    }
  }
}
/* ]]> */
EOF;
  }

  /**
   * Gets the stylesheet code to inject in the head tag.
   *
   * @param string The stylesheet code
   */
  public function getStylesheet()
  {
    return <<<EOF
#sfWebDebug
{
  padding: 0;
  margin: 0;
  font-family: Arial, sans-serif;
  font-size: 12px;
  color: #333;
  text-align: left;
  line-height: 12px;
}

#sfWebDebug a, #sfWebDebug a:hover
{
  text-decoration: none;
  border: none;
  background-color: transparent;
  color: #000;
}

#sfWebDebug img
{
  float: none;
  margin: 0;
  border: 0;
  display: inline;
}

#sfWebDebugBar
{
  position: absolute;
  margin: 0;
  padding: 1px 0;
  right: 0px;
  top: 0px;
  opacity: 0.80;
  filter: alpha(opacity:80);
  z-index: 10000;
  white-space: nowrap;
  background-color: #ddd;
}

#sfWebDebugBar[id]
{
  position: fixed;
}

#sfWebDebugBar img
{
  vertical-align: middle;
}

#sfWebDebugBar .sfWebDebugMenu
{
  padding: 5px;
  padding-left: 0;
  display: inline;
  margin: 0;
}

#sfWebDebugBar .sfWebDebugMenu li
{
  display: inline;
  list-style: none;
  margin: 0;
  padding: 0 6px;
}

#sfWebDebugBar .sfWebDebugMenu li.last
{
  margin: 0;
  padding: 0;
  border: 0;
}

#sfWebDebugDatabaseDetails li
{
  margin: 0;
  margin-left: 30px;
  padding: 5px 0;
}

#sfWebDebugShortMessages li
{
  margin-bottom: 10px;
  padding: 5px;
  background-color: #ddd;
}

#sfWebDebugShortMessages li
{
  list-style: none;
}

#sfWebDebugDetails
{
  margin-right: 7px;
}

#sfWebDebug pre
{
  line-height: 1.3;
  margin-bottom: 10px;
}

#sfWebDebug h1
{
  font-size: 16px;
  font-weight: bold;
  margin: 20px 0;
  padding: 0;
  border: 0px;
  background-color: #eee;
}

#sfWebDebug h2
{
  font-size: 14px;
  font-weight: bold;
  margin: 10px 0;
  padding: 0;
  border: 0px;
  background: none;
}

#sfWebDebug h3
{
  font-size: 12px;
  font-weight: bold;
  margin: 10px 0;
  padding: 0;
  border: 0px;
  background: none;
}

#sfWebDebug .sfWebDebugTop
{
  position: absolute;
  left: 0px;
  top: 0px;
  width: 98%;
  padding: 0 1%;
  margin: 0;
  z-index: 9999;
  background-color: #efefef;
  border-bottom: 1px solid #aaa;
}

#sfWebDebugLog
{
  margin: 0;
  padding: 3px;
  font-size: 11px;
}

#sfWebDebugLogMenu
{
  margin-bottom: 5px;
}

#sfWebDebugLogMenu li
{
  display: inline;
  list-style: none;
  margin: 0;
  padding: 0 5px;
  border-right: 1px solid #aaa;
}

#sfWebDebugConfigSummary
{
  display: inline;
  padding: 5px;
  background-color: #ddd;
  border: 1px solid #aaa;
  margin: 20px 0;
}

#sfWebDebugConfigSummary li
{
  list-style: none;
  display: inline;
  margin: 0;
  padding: 0 5px;
}

#sfWebDebugConfigSummary li.last
{
  border: 0;
}

.sfWebDebugInfo, .sfWebDebugInfo td
{
  background-color: #ddd;
}

.sfWebDebugWarning, .sfWebDebugWarning td
{
  background-color: orange !important;
}

.sfWebDebugError, .sfWebDebugError td
{
  background-color: #f99 !important;
}

.sfWebDebugLogNumber
{
  width: 1%;
}

.sfWebDebugLogType
{
  width: 1%;
  white-space: nowrap;
}

.sfWebDebugLogType, #sfWebDebug .sfWebDebugLogType a
{
  color: darkgreen;
}

#sfWebDebug .sfWebDebugLogType a:hover
{
  text-decoration: underline;
}

.sfWebDebugLogInfo
{
  color: blue;
}

.ison
{
  color: #3f3;
  margin-right: 5px;
}

.isoff
{
  color: #f33;
  margin-right: 5px;
  text-decoration: line-through;
}

.sfWebDebugLogs
{
  padding: 0;
  margin: 0;
  border: 1px solid #999;
  font-family: Arial;
  font-size: 11px;
}

.sfWebDebugLogs tr
{
  padding: 0;
  margin: 0;
  border: 0;
}

.sfWebDebugLogs td
{
  margin: 0;
  border: 0;
  padding: 1px 3px;
  vertical-align: top;
}

.sfWebDebugLogs th
{
  margin: 0;
  border: 0;
  padding: 3px 5px;
  vertical-align: top;
  background-color: #999;
  color: #eee;
  white-space: nowrap;
}

.sfWebDebugDebugInfo
{
  color: #999;
  font-size: 11px;
  margin: 5px 0 5px 10px;
  padding: 2px 0 2px 5px;
  border-left: 1px solid #aaa;
  line-height: 1.25em;
}

.sfWebDebugDebugInfo .sfWebDebugLogInfo,
.sfWebDebugDebugInfo a.sfWebDebugFileLink
{
  color: #333 !important;
}

.sfWebDebugCache
{
  padding: 0;
  margin: 0;
  font-family: Arial;
  position: absolute;
  overflow: hidden;
  z-index: 995;
  font-size: 9px;
  padding: 2px;
  filter:alpha(opacity=85);
  -moz-opacity:0.85;
  opacity: 0.85;
}

#sfWebDebugSymfonyVersion
{
  margin-left: 0;
  padding: 1px 4px;
  background-color: #666;
  color: #fff;
}

#sfWebDebugviewDetails ul
{
  padding-left: 2em;
  margin: .5em 0;
  list-style: none;
}

#sfWebDebugviewDetails li
{
  margin-bottom: .5em;
}

#sfWebDebug .sfWebDebugDataType,
#sfWebDebug .sfWebDebugDataType a
{
  color: #666;
  font-style: italic;
}

#sfWebDebug .sfWebDebugDataType a:hover
{
  text-decoration: underline;
}

#sfWebDebugDatabaseLogs
{
  margin-bottom: 10px;
}

#sfWebDebugDatabaseLogs ol
{
  margin: 0;
  padding: 0;
  margin-left: 20px;
  list-style: decimal;
}

#sfWebDebugDatabaseLogs li
{
  padding: 6px;
}

#sfWebDebugDatabaseLogs li:nth-child(odd)
{
  background-color: #CCC;
}

.sfWebDebugDatabaseQuery
{
  margin-bottom: .5em;
  margin-top: 0;
}

.sfWebDebugDatabaseLogInfo
{
  color: #666;
  font-size: 11px;
}

.sfWebDebugDatabaseQuery .sfWebDebugLogInfo
{
  color: #909;
  font-weight: bold;
}

.sfWebDebugHighlight
{
  background: #FFC;
}
EOF;
  }
}
