<?php

/*
 * This file is part of the symfony package.
 * (c) 2007 Roman Zajac <roman@inventis.pl>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Wrapper for YUI Treeview component.
 * 
 * @package    symfony
 * @subpackage helper
 * @author     Roman Zajac <roman@inventis.pl>
 * @version    SVN: $Id$
 */

/**
 * Include libraries for Treeview.
 * Helps solving problem in junction with AJAX
 * (because remote_function, etc doesn't decorate ajax calls
 *  and doesn't include javascript libraries,
 *  yui_include_treeview. must be called in script from which you call ajax,
 *  and the main helper - yui_treeview in remote script)
 *
 * Example usage:
 *
 *   // in action that include ajax call for treeview action
 *   < ?php yui_include_treeview() ? >
 *   <div id="categories_pane"></div>
 *   < ?php echo javascript_tag(
 *     remote_function(array(
 *           'update' => 'categories_pane',
 *           'url'    => 'admin/categoriesTree',
 *           'script' => 'true',
 *           )
 *     )
 *   ) ? > 
 *   
 */

function yui_include_treeview()
{
  use_helper('Javascript');

  sfYUI::addComponent('yahoo');
  sfYUI::addComponent('event');
  sfYUI::addComponent('treeview');
  
  sfYUI::addStylesheet('treeview', 'tree');
}

/**
 * Renders a Treeview.
 *
 * Example usage:
 *
 *   < ?php
 * 
 *
 *   $tree = array(
 *                0 => array(
 *                  'text' => 'foo 1',
 *                  'id' => 'node_1',
 *                  'url' => /url1,
 *                  'children' => array(
 *                                  0 => array(
 *                                            'text' => 'foo 1.1',
 *                                            'id' => 'node_1_1',
 *                                            'url' => /url1/1,
 *                                            'children' => array(
 *                                              )
 *                                            )  
 *                   )
 *                 ) 
 *                1 => array(
 *                  'text' => 'foo 2',
 *                  'id' => 'node_2',
 *                  'url' => /url1,
 *                 ) 
 *                2 => array(
 *                  'text' => 'foo 3',
 *                  'id' => 'node_3',
 *                  'url' => /url1,
 *                 ) 
 *   );
 *
 *   $tree_name = 'categories';
 *   $append_to = 'tree_div';
 *
 *   ? >
 *
 *   < ?php echo yui_treeview($tree_name, $append_to, $tree) ? >
 *   <div id="tree_div"></div>
 *   
 * Excellent also for work with Doctrine Tree Iterator (to tell the true
 * written for it)  
 *   
 * @param string $append_to
 * @param array $tree
 * @return string
 */

function yui_treeview($name, $append_to, $tree = array())
{
  yui_include_treeview();

  $treeView = yui_treeview_varname($name);


  $js = "";
  $js .= "var ".$treeView.";\n";
  $js .= "treeInit = function() {\n";
  $js .= "	".$treeView." = new YAHOO.widget.TreeView('".$append_to."');\n";
  $js .= "	var root = ".$treeView.".getRoot();\n";
  foreach ($tree as $node)
  {
    $js .= yui_treeview_node( $node, $name );
  }
  $js .= "	".$treeView.".draw();\n";
  $js .= "}\n";

  $js .= "YAHOO.util.Event.onContentReady('". $append_to ."', treeInit );\n";
  
  // watch above event - sometimes dosn't show tree, quickfix: uncomment following line
  //$js .= "treeInit();\n";

  return javascript_tag($js);
}


function yui_treeview_node( $node, $tree_name, $parent_id = false )
{
  $node_id = ($parent_id?$parent_id."_":"").$node["id"];
  $branch_name = $tree_name."_".$node_id;
  
  $js = "";
  
  $js .= "  var myobj = { label: '".$node["text"]."'"
        ."   , id: '".$branch_name."'"
        ."   , href: '".$node["url"]."'"
        ."  };\n";
  $js .= "	var node_".$node_id." = new YAHOO.widget.TextNode(myobj, ". ($parent_id ? "node_".$parent_id : "root").", false);\n";
  
  
  if( (isset($node['children']) && is_array($node['children'])) || (is_object($node) && $node->getChildren()) )
  {
    foreach($node['children'] as $child)
    {
      // FIXME: first element in children collection is empty (?)
      if( !$child['id'] )
      {
        continue;
      }
      $js .= yui_treeview_node( $child, $tree_name, $node_id );
    }
  }else
  {
    $js .= "	// id=".$node_id." has no children\n";
  }
  return $js;
  
}


function yui_treeview_varname($name)
{
  return $name."_treeView";
}
