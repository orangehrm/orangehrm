<?php

/*
 * This file is part of the symfony package.
 * (c) 2006 Nick Winfield <enquiries@superhaggis.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Wrapper for YUI Tabview component.
 * 
 * @package    symfony
 * @subpackage helper
 * @author     Nick Winfield <enquiries@superhaggis.com>
 * @version    SVN: $Id$
 */

/**
 * Mark the start of the Tabview block.
 * Combined with yui_tabview_end() in order to capture a block 
 * of designated Tabview HTML.
 *
 * Example usage:
 *
 *   <?php yui_tabview_start('demo') ?>
 *   <div id="demo" class="yui-navset">
 *     <ul class="yui-nav">
 *       <li class="selected"><a href="#tab1"><em>Tab One Label</em></a></li>
 *       <li><a href="#tab2"><em>Tab Two Label</em></a></li>
 *       <li><a href="#tab3"><em>Tab Three Label</em></a></li>
 *     </ul>            
 *     <div class="yui-content">
 *       <div><p>Tab One Content</p></div>
 *       <div><p>Tab Two Content</p></div>
 *       <div><p>Tab Three Content</p></div>
 *     </div>
 *   </div>
 *   <?php yui_tabview_end('demo') ?>
 * 
 * @param string $element
 */
function yui_tabview_start($element)
{
  sfYUI::addComponent('yahoo');
  sfYUI::addComponent('dom');
  sfYUI::addComponent('event');
  sfYUI::addComponent('tabview');
   
  sfYUI::addStylesheet('tabview', 'tabs');
  sfYUI::addStylesheet('tabview', 'border_tabs');

  ob_start();
}

/**
 * Mark the end of the Tabview block.
 * @param string $element
 * @param array $options
 */
function yui_tabview_end($element, $options = array())
{
  use_helper('Javascript');

  $content = ob_get_clean();

  $js = "var " . $element . "_tabView = new YAHOO.widget.TabView('" . $element . "', { ";
	
	if(isset($options['orientation'])){
		$js .= $orientation = "orientation: " . "'".$options['orientation']."'";
	}
	
	$js .= " });";

  echo javascript_tag($js) . "\n" . $content;
}

/**
 * Renders a Tabview using Javascript and sets a default 
 * width if left unspecified.
 *
 * Example usage:
 *
 *   <?php
 * 
 *   $tabs = array();
 *
 *   $tabs[] = array(
 *     'label' => 'foo',
 *     'content' => 'hello world!',
 *     'active' => true,
 *   );
 *
 *   $tabs[] = array(
 *     'label' => 'bar',
 *     'content' => 'another tab!',
 *   );
 *
 *   $tabs[] = array(
 *     'label' => 'hello',
 *     'content' => 'third one!',
 *   );
 *
 *   $element = 'newtabview';
 *   $append_to = 'tabview_anchor';
 *   $width = '30em';
 *
 *   ?>
 *
 *   <?php echo yui_tabview_from_js($element, $append_to, $tabs, $width) ?>
 *   <div id="tabview_anchor"></div>
 *   
 * @param string $element
 * @param string $append_to
 * @param array $tabs
 * @param string $width
 * @return string
 */

function yui_tabview_from_js($element, $append_to, $tabs = array(), $width = '30em')
{
  use_helper('Javascript');

  sfYUI::addComponent('yahoo');
  sfYUI::addComponent('dom');
  sfYUI::addComponent('event');
  sfYUI::addComponent('tabview');

  sfYUI::addStylesheet('tabview', 'tabs');
  sfYUI::addStylesheet('tabview', 'border_tabs');

  $tabView = $element."_tabView";

  $js  = "";
  $js .= "var " . $tabView . " = new YAHOO.widget.TabView( { id: '" . $element . "' } );\n";
  $js .= $tabView.".setStyle('width', '" . $width . "');\n";

  foreach ($tabs as $tab)
  {
    $tab_attributes = array();

    $js .= $tabView.".addTab(new YAHOO.widget.Tab({\n";

    $tab_attributes[] = "label: '" . $tab['label'] . "'";

    if (isset($tab['active']))
    {
      $tab_attributes[] = "active: true";
    }

    if (isset($tab['content']))
    {
      $tab_attributes[] = "content: '" . $tab['content'] . "'";
    } 
    elseif (isset($tab['dataSrc']))
    {
      $tab_attributes[] = "dataSrc: '" . $tab['dataSrc'] . "'";
      if (isset($tab['cacheData']))
      {
        $tab_attributes[] = "cacheData: true";
      }
    }

    $js .= "  " . join(', ', $tab_attributes) . "\n";
    $js .= "}));\n\n";
  }  

  $js .= "YAHOO.util.Event.onContentReady('" . $append_to . "', function() { " . $tabView . ".appendTo(this); });";

  return javascript_tag($js);
}
