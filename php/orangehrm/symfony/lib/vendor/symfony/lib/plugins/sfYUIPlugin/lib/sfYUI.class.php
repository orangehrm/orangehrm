<?php

/*
 * This file is part of the symfony package.
 * (c) 2006 Nick Winfield <enquiries@superhaggis.com>
 * (c) 2006 Pierre Minnieur <pm@pierre-minnieur.de>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfYUI adds the components javascript libraries to the current response
 * and takes care of not to include them twice.
 * 
 * @package    symfony
 * @subpackage helper
 * @author     Nick Winfield <enquiries@superhaggis.com>
 * @author     Pierre Minnieur <pm@pierre-minnieur.de>
 * @version    SVN: $Id: sfYUI.class.php 2737 2006-11-17 15:22:49Z pminnieur $
 */
class sfYUI
{
  /**
   * Holds a list of already included components to prevent including the
   * same component twice.
   *
   * @var array
   */
  private static $included_components = array();

  /**
   * Holds a list of already included stylesheets to prevent including the
   * same stylesheet twice.
   *
   * @var array
   */
  private static $included_stylesheets = array();  

  /**
   * Adds a component to the list of included javascripts.
   *
   * @param string $name The Yahoo! UI components name to include.
   * @return void
   */
  public static function addComponent($name)
  {
    // check if the component is already included
    if (!in_array($name, self::$included_components))
    {
      // add javascript to the response
      sfContext::getInstance()->getResponse()->addJavascript(sprintf('%1$s/%2$s/%2$s%3$s.js',
        sfConfig::get('sf_yui_js_dir'),
        $name,
        sfConfig::get('sf_yui_js_suffix')
      ));
      
      // add component to the included list
      array_push(self::$included_components, $name);
    }
  }

  /**
   * Adds a component stylesheet to the list of included stylesheets.
   *
   * @param string $component The Yahoo! UI components name.
   * @param string $stylesheet The Yahoo! UI components stylesheet name to include.
   * 
   */
  public static function addStylesheet($component, $stylesheet)
  {
    $include = sprintf('%s_%s', $component, $stylesheet);
    
    // check if the stylesheet is already included
    if (!in_array($include, self::$included_stylesheets))
    {
      // add stylesheet to the response
      sfContext::getInstance()->getResponse()->addStylesheet(sprintf('%s/%s/assets/%s.css',
        sfConfig::get('sf_yui_js_dir'),
        $component,
        $stylesheet
      ));
      
      // add stylesheet to the included list
      array_push(self::$included_stylesheets, $include);
    }    
  }
}
