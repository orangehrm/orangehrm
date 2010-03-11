<?php

/*
  * This file is part of the symfony package.
  * (c) 2007 Dave Dash <dave.dash@spindrop.us>
  * 
  * For the full copyright and license information, please view the LICENSE
  * file that was distributed with this source code.
  */

/**
  * Wrapper for yui-ext library
  * 
  * @package    symfony
  * @subpackage helper
  * @author     Dave Dash <dave.dash@spindrop.us>
  * @version    SVN: $Id:  $
  */

/**
  * Shows a link to toggle the visibility of another element.
  *
  * @param string $show_text
  * @param string $hide_text
  * @param string $element
  * @param array $options
  * @return string $link
  */
function yuiext_toggle_link($show_text, $hide_text, $element, $options = array())
{
  use_helper('Javascript');
  sfYUI::addComponent('yahoo');
  sfYUI::addComponent('dom');
  sfYUI::addComponent('event');
  // need to add yui-ext
  sfContext::getInstance()->getResponse()->addJavascript(sprintf('%1$s/%2$s.js',
    sfConfig::get('sf_yuiext_js_dir'),
    'yui-ext'
    ));

  $func = 'new function() {
    var actor = new YAHOO.ext.Actor("'.$element.'");
    actor.enableDisplayMode();
    var target = event.target || event.srcElement;
    if (actor.isVisible()) {
      actor.hide();
      target.innerHTML = "'.$show_text.'";
    } else {
      actor.show();
      target.innerHTML = "'.$hide_text.'";
    }
  }';


	// make a function that changes the text accordingly
	$link  = link_to_function($show_text, $func, $options);
	return $link;
}

