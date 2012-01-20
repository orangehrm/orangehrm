<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

require_once ROOT_PATH . '/lib/common/menu/MenuRenderer.php';

/**
 * Menu renderer for orange theme
 */
class Menu implements MenuRenderer {

	public function getMenuHeight() {
		return 40;
	}
	
	public function getMenuWidth() {
		return 200;
	}
	
	public function getCSS() {
?>		
	<link href="themes/beyondT/menu/menu.css" rel="stylesheet" type="text/css">
<?php	 	
	}
	
	public function getJavascript($menu) {
?>
<script language="JavaScript" src="scripts/ypSlideOutMenus.js"></script>
<script language="JavaScript">
//window.onresize = setSize();

		var yPosition = 108;

		var agt=navigator.userAgent.toLowerCase();

		var xPosition = 150;

		if (agt.indexOf("konqueror") != -1) var xPosition = 144;

		if (agt.indexOf("windows") != -1) var xPosition = 144;

		if (agt.indexOf("msie") != -1) var xPosition = 150;


		new ypSlideOutMenu("menu1", "right", xPosition, yPosition, 150, 230)
		new ypSlideOutMenu("menu2", "right", xPosition, yPosition + 22, 146, 360)
		new ypSlideOutMenu("menu3", "right", xPosition, yPosition + 44, 146, 220)
		new ypSlideOutMenu("menu4", "right", xPosition, yPosition + 66, 146, 80)
		new ypSlideOutMenu("menu5", "right", xPosition, yPosition + 88, 146, 130)
		new ypSlideOutMenu("menu9", "right", xPosition, yPosition + 110, 146, 80)
		new ypSlideOutMenu("menu12", "right", xPosition, yPosition + 132, 146, 120)
		new ypSlideOutMenu("menu15", "right", xPosition, yPosition + 154, 146, 120)
		new ypSlideOutMenu("menu17", "right", xPosition, yPosition + 176, 146, 120)
		new ypSlideOutMenu("menu18", "right", xPosition, yPosition + 198, 146, 120)//CVS
		new ypSlideOutMenu("menu13", "right", xPosition, yPosition, 146, 120)
		new ypSlideOutMenu("menu14", "right", xPosition, yPosition + 22, 146, 120)
		new ypSlideOutMenu("menu16", "right", xPosition, yPosition, 146, 120)
		new ypSlideOutMenu("menu19", "right", xPosition, yPosition, 146, 140)//HSP
		new ypSlideOutMenu("menu20", "right", xPosition, yPosition + 16, 146, 120)

function swapImgRestore() {
  var i,x,a=document.sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}
function preloadImages() {
  var d=document; if(d.images){ if(!d.p) d.p=new Array();
    var i,j=d.p.length,a=preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.p[j]=new Image; d.p[j++].src=a[i];}}
}
function findObj(n, d) {
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=findObj(n,d.layers[i].document);
  if(!x && document.getElementById) x=document.getElementById(n); return x;
}
function swapImage() {
  var i,j=0,x,a=swapImage.arguments; document.sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=findObj(a[i]))!=null){document.sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
function showHideLayers() {
  var i,p,v,obj,args=showHideLayers.arguments;
  for (i=0; i<(args.length-2); i+=3) if ((obj=findObj(args[i]))!=null) { v=args[i+2];
    if (obj.style) { obj=obj.style	; v=(v=='show')?'visible':(v='hide')?'hidden':v; }
    obj.visibility=v; }
}

function setSize() {
	var iframeElement = document.getElementById('rightMenu');
	iframeElement.style.height = (window.innerHeight - 20) + 'px'; //100px or 100%
	iframeElement.style.width = '100%'; //100px or 100%
}
</script>	
<?php		
	}
	
	public function getMenu($menu, $optionMenu, $welcomeMessage) {
?>

<div style="clear:left;">
<table width="100%" cellspacing="0" cellpadding="0" border="0">
  <tr height="20" class="tabRow">
    <td class="tabLeftSpace"><img src="themes/beyondT/pictures/blank.gif" width="8" height="1" border="0" alt=""></td>
 <?php
	if (!empty($menu)) {
		foreach ($menu as $menuItem) {
			$targetVal = $menuItem->getTarget();
			$target = empty($targetVal) ? '' : ' target= "' . $targetVal . '" ';
			
			$subItems = $menuItem->getSubMenuItems();
			$current = ($menuItem->isCurrent()) ? ' current' : '';
?>			
<td>
  <table cellspacing="0" cellpadding="0" border="0" class="tabContainer">
    <tr height="20">
    <td class="<?php echo $current ? 'currentTabLeft' : 'otherTabLeft';?>" width="8"><img src="themes/beyondT/pictures/blank.gif" width="8" height="1" border="0" alt=""></td> 
    <td class="<?php echo $current ? 'currentTab' : 'otherTab';?>" nowrap>
    <a class="<?php echo $current ? 'currentTab' : 'otherTab';?>"  href="<?php echo $menuItem->getLink();?>" <?php echo $target;?> ><?php echo $menuItem->getMenuText();?></a></td>
      <td class="<?php echo $current ? 'currentTabRight' : 'otherTabRight';?>" width="8"><img src="themes/beyondT/pictures/blank.gif" width="8" height="1" border="0" alt=""></td>
      <td class="tabSpace" width="1"><img src="themes/beyondT/pictures/blank.gif" width="1" height="1" border="0" alt=""></td>
    </tr>
  </table>
</td>

<?php						
		}
	}	
?>
  <td width="100%" class="tabSpace"><img src="" width="1" height="1" border="0" alt=""></td>
</tr>
</table>
<table width="100%" cellspacing="0" cellpadding="0" border="0">
          <tr height="20">
            <input type="hidden" name="action" value="UnifiedSearch">
            <input type="hidden" name="module" value="Home">
            <input type="hidden" name="search_form" value="false">
            <td class="subTabBar" colspan="2"><table width="100%" cellspacing="0" cellpadding="0" border="0" height="20">
                <tr>
                  <td class="welcome" width="100%"><?php echo $welcomeMessage;; ?></td>
                  <td class="search" align="right" nowrap="nowrap">
                  
                  <?php 
                  if ($optionMenu) {
                  	  $count = 0;
				      foreach ($optionMenu as $optionItem) {
				      	$count++;
				      	if ($count > 1) {
				  ?>
				  			<td class="searchSeparator">&nbsp;</td>
				  <?php
				      	}				      	
				  ?>
							<td class="search" style="padding: 0px" align="right" nowrap="nowrap">
								&nbsp;&nbsp;<a href="<?php echo $optionItem->getLink();?>"><strong><?php echo $optionItem->getMenuText(); ?></strong></a></td>
				  <?php
				      	
				      }		
                  }
				  ?>
                  <td class="search" nowrap>&nbsp;&nbsp; </td>
                </tr>
            </table></td>
          </tr>

</table>
</div>
<div id="left-menu" style="float:left;display:block;height:100px;width:200px;border:1px solid red;">

</div>
<?php		
	}
}
?>
