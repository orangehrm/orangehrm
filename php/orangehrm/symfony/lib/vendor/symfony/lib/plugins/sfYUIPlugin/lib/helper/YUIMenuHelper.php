<?php

/*
 * This file is part of the symfony package.
 * (c) 2007 Roman Zajac <roman@inventis.pl>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Wrapper for YUI Menu component.
 * 
 * @package    symfony
 * @subpackage helper
 * @author     Roman Zajac <roman@inventis.pl>
 * @version    SVN: $Id$
 */

/**
 * Include libraries for Menu.
 * Helps solving problem in junction with AJAX
 * (because remote_function, etc doesn't decorate ajax calls
 *  and doesn't include javascript libraries,
 *  yui_include_menu. must be called in script from which you call ajax,
 *  and the main helper - yui_menu in remote script)
 *
 * Example usage:
 *
 *   // in action that include ajax call for menu action
 *   < ?php yui_include_menu() ? >
 *   <div id="categories_pane"></div>
 *   < ?php echo javascript_tag(
 *     remote_function(array(
 *           'update' => 'categories_pane',
 *           'url'    => 'admin/categoriesMenu',
 *           'script' => 'true',
 *           )
 *     )
 *   ) ? > 
 *   
 */

function yui_include_menu()
{
  use_helper('Javascript');

  sfYUI::addComponent('yahoo');
  sfYUI::addComponent('dom');
  sfYUI::addComponent('event');
  sfYUI::addComponent('container');
  sfYUI::addComponent('menu');
  
  sfYUI::addStylesheet('menu', 'menu');
}

/**
 * Renders a Menu.
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
 *   $append_to = 'menu_div';
 *
 *   ? >
 *
 *   < ?php echo yui_menu($tree_name, $append_to, $tree) ? >
 *   <div id="menu_div"></div>
 *   
 * Like Treeview working with Doctrine Tree Iterator  
 *   
 * @param string $append_to
 * @param array $tree
 * @return string
 */

function yui_menu($name, $append_to, $menuArray = array())
{
  yui_include_menu();

  $oMenu = $name."Menu";


  $js = "";
  $js .= $oMenu."Init = function() {\n";
  $js .= "  var ".$oMenu." = new YAHOO.widget.Menu('".$name."');\n";
  $js .= "  var ".$oMenu."Data = [\n";
  foreach ($menuArray as $item)
  {
    $js .= yui_menu_item( $item, $name );
  }
  $js .= "  ];\n";
  $js .= "  ".$oMenu.".addItems(".$oMenu."Data);\n";
  $js .= "  ".$oMenu.".render(document.getElementById('".$append_to."'));\n";
  $js .= "  ".$oMenu.".showEvent.subscribe(function() {this.focus();});\n";
  $js .= "  ".$oMenu.".show();\n";
  $js .= "  YAHOO.util.Event.addListener('menutoggle', 'click', ".$oMenu.".show, null, ".$oMenu.");\n";
  $js .= "}\n";

  // watch this event - sometimes dosn't show tree, quickfix: comment following line
  $js .= "YAHOO.util.Event.onContentReady('". $append_to ."', ".$oMenu."Init );\n";
  //$js .= $oMenu."Init();\n";

  return javascript_tag($js);
}


function yui_contextmenu($name, $append_to, $menuArray = array())
{
  yui_include_menu();

  $oMenu = $name."ContextMenu";


  $js = "";
  $js .= "".$oMenu."CurrentNode = null;\n";   // The YAHOO.widget.TextNode instance whose "contextmenu" DOM event triggered the display of the context menu, without "var" so it's global
  $js .= "".$oMenu."NodeMap = {};\n";         // Hash of YAHOO.widget.TextNode instances in the tree, without "var" so it's global
  $js .= $oMenu."Init = function() {\n";
  $js .= "  var ".$oMenu." = new YAHOO.widget.ContextMenu('".$name."', { trigger: '".$append_to."', lazyload: true });\n";
  $js .= "  var ".$oMenu."Data = [\n";
  foreach ($menuArray as $item)
  {
    $js .= yui_menu_item( $item, $name );
  }
  $js .= "  ];\n";
  $js .= "  ".$oMenu.".addItems(".$oMenu."Data);\n";
  $js .= "  ".$oMenu.".render('".$append_to."');\n";
  $js .= yui_contextmenu_handler_for_treeview($oMenu, $append_to);
  $js .= "  ".$oMenu.".triggerContextMenuEvent.subscribe(onTriggerContextMenu, ".$oMenu.", true);\n";
  //$js .= "  YAHOO.util.Event.addListener('menutoggle', 'click', ".$oMenu.".show, null, ".$oMenu.");\n";
  $js .= "}\n";

  // watch this event - sometimes dosn't show tree, quickfix: comment following line
  $js .= "YAHOO.util.Event.onContentReady('". $append_to ."', ".$oMenu."Init );\n";
  //$js .= $oMenu."Init();\n";

  return javascript_tag($js);
}


function yui_menu_item( $item, $menu_name, $parent_id = false )
{
  $item_id = ($parent_id?$parent_id."_":"").$item["id"];
  $submenu_name = $menu_name."_".$item_id;
  $js = "";
  
  
  $js .= "  { text: '".$item["text"]."'"
        ."   , id: '".$item_id."'"
        .(yui_menu_item_check_property($item, "helptext")  ? "   , helptext: '".$item["helptext"]."'" : "")
        .(yui_menu_item_check_property($item, "url")       ? "   , url: '".$item["url"]."'" : "")
        .(yui_menu_item_check_property($item, "target")    ? "   , target: '".$item["target"]."'" : "")
        .(yui_menu_item_check_property($item, "emphasis")  ? "   , emphasis: ".$item["emphasis"] : "")
        .(yui_menu_item_check_property($item, "strongemphasis") ? "   , strongemphasis: ".$item["strongemphasis"] : "")
        .(yui_menu_item_check_property($item, "disabled")  ? "   , disabled: ".$item["disabled"] : "")
        .(yui_menu_item_check_property($item, "selected")  ? "   , selected: ".$item["selected"] : "")
        .(yui_menu_item_check_property($item, "checked")   ? "   , checked: ".$item["checked"] : "")
        .(yui_menu_item_check_property($item, "onclick")   ? "   , onclick: ".$item["onclick"] : "")
        .(yui_menu_item_check_property($item, "classname") ? "   , classname: '".$item["classname"]."'" : "")
        ;





  if( (isset($item['children']) && is_array($item['children']) && count($item['children']))
      || (is_object($item) && $item->getChildren()) )
  {
    $js .= "   , submenu: { id: '".$submenu_name."', itemdata: [\n";
    foreach($item['children'] as $child)
    {
      // FIXME: With iterators first element in children collection is empty (?)
      if( !$child['id'] )
      {
        continue;
      }
      $js .= yui_menu_item( $child, $submenu_name, $item_id );
    }
    $js .= "   ] } \n";
  }else
  {
    //$js .= "  /"."* id=".$item['id']." has no children *"."/\n";
  }

  $js .= "  },\n";
  
  return $js;
  
}

/*
    "contextmenu" event handler for the element(s) that triggered the display of the context menu
*/
function yui_contextmenu_handler_for_treeview($js_contextmenu, $element_name)
{ 
  $js  = "    \n";
  $js .= "  function onTriggerContextMenu(p_oEvent, p_oMenu)\n";
  $js .= "  {\n";
  $js .= "    // Returns a TextNode instance that corresponds to the DOM element whose contextmenu event triggered the display of the context menu.\n";
  $js .= "    function GetTextNodeFromEventTarget(p_oTarget)\n";
  $js .= "    {\n";
  $js .= "        if(p_oTarget.tagName.toUpperCase() == 'A' && p_oTarget.className == 'ygtvlabel') {\n";
  $js .= "            return ".$js_contextmenu."NodeMap[p_oTarget.id];\n";
  $js .= "        }\n";
  $js .= "        else\n";
  $js .= "        {\n";
  $js .= "            if(p_oTarget.parentNode)\n";
  $js .= "            {\n";
  $js .= "                return GetTextNodeFromEventTarget(p_oTarget.parentNode);\n";
  $js .= "            }\n";
  $js .= "        }\n";
  $js .= "    }\n";
  $js .= "    \n";
  $js .= "    YAHOO.widget.TreeView.prototype.getNodes = function(){return this._nodes;};\n";
  $js .= "    var tree = YAHOO.widget.TreeView.getTree('".$element_name."');\n";
  $js .= "    //".$js_contextmenu."NodeMap = tree._nodes;\n";
  $js .= "    var nodesCollection = tree.getNodes();\n";
  $js .= "    for(nodeIndex in nodesCollection)";
  $js .= "    {\n";
  $js .= "      var nodeObj = nodesCollection[nodeIndex];\n";
  $js .= "      ".$js_contextmenu."NodeMap[nodeObj.labelElId] = nodeObj;\n";
  $js .= "    };\n";
  $js .= "    \n";
  $js .= "    var oTextNode = GetTextNodeFromEventTarget(this.contextEventTarget);\n";
  $js .= "    if(oTextNode)\n";
  $js .= "    {\n";
  $js .= "       window.status='.. oTextNode='+oTextNode;\n";
  $js .= "      ".$js_contextmenu."CurrentNode = oTextNode;\n";
  $js .= "    }\n";
  $js .= "    else\n";
  $js .= "    {\n";
  $js .= "       window.status='.. cancel';\n";
  $js .= "       this.cancel();\n";
  $js .= "    }\n"; 
  $js .= "  }\n";
  
  return $js;
}



function yui_menu_item_check_property($item, $property_name)
{
  try
  {
    return @$item[$property_name];
  }catch(Exception $e)
  {
    return false;
  }
}
