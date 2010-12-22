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
 *
 */

require_once ROOT_PATH . '/lib/common/menu/MenuRenderer.php';

/**
 * Menu renderer for orange theme
 */
class Menu implements MenuRenderer {
	
	public function getCSS() {
?>	
	<link href="themes/orange/menu/menu.css" rel="stylesheet" type="text/css"/>
	
<?php /* Fix for the IE6 problems with :hover support etc. 
         Shouldn't really be needed for IE7, but doesn't work in that browser as well, without following CSS. */
?>
<!--[if IE]>
<link href="themes/orange/menu/IE6_menu.css" rel="stylesheet" type="text/css">
<![endif]--> 	

<!--[if lte IE 6]>
<style type="text/css">
#top-menu {
    /*width:expression(document.body.clientWidth < 1000 ? "900px" : "100%" );*/
}
</style>
<![endif]--> 	
 

<?php	 	
	}
	
	public function getJavascript($menu) {
?>
<script type="text/javaScript"><!--//--><![CDATA[//><!--
var dropdownMenuHidden = false;

function menuExited() {
	var topUl = document.getElementById('nav');
	var uls = topUl.getElementsByTagName('ul');
	for(var i=0; i<uls.length; i++) {
	    ul = uls[i];
	    ul.style.left = '-999em';
	}
	dropdownMenuHidden = true;	
}

function menuclicked(item) {
	var topUl = document.getElementById('nav');
	var uls = topUl.getElementsByTagName('ul');
	for(var i=0; i<uls.length; i++) {
	    ul = uls[i];
	    ul.style.left = '-999em';
	}
	dropdownMenuHidden = true;	
}

function topMenuHover() {
    if (dropdownMenuHidden) {
		var topUl = document.getElementById('nav');
		var uls = topUl.getElementsByTagName('ul');
		for(var i=0; i<uls.length; i++) {
		    ul = uls[i];
		    ul.style.left = '';
		}        
    }
}
//--><!]]></script>


	
<?php 
/* Fix for the IE6 "Background image flicker bug". (http://www.mister-pixel.com) */
?>
<!--[if lte IE 6]>
<script language="JavaScript">
try {
  document.execCommand("BackgroundImageCache", false, true);
} catch(err) {}
</script>
<![endif]--> 
	
<?php		
	}
	
	public function getMenu($menu, $optionMenu, $welcomeMessage) {
?>

<!--  show menu -->
<div id="option-menu-bar">
<ul id="option-menu">
     <li><?php echo $welcomeMessage;; ?></li>
<?php 
	 if ($optionMenu) {
	      foreach ($optionMenu as $optionItem) {
?>
	 <li><a href="<?php echo $optionItem->getLink();?>" target="rightMenu"><?php echo $optionItem->getMenuText(); ?></a></li>
<?php   
		}
	 }    
?>
	
</ul>
</div>     
                  
<div id="top-menu">
<ul id="nav" onmouseout="menuExited();">

<?php
	if (!empty($menu)) {
		foreach ($menu as $menuItem) {
			$targetVal = $menuItem->getTarget();
			$target = empty($targetVal) ? '' : ' target= "' . $targetVal . '" ';
			
			$subItems = $menuItem->getSubMenuItems();
			$spanClass = (!empty($subItems)) ? 'drop' : '';
			if ($menuItem->isCurrent()) {
				if (!empty($spanClass)) {
				    $spanClass .= ' current';
				} else {
			    	$spanClass .= 'current';
				}
			}
			
?>			
			<li class="l1" id="<?php echo $menuItem->getIcon(); ?>" onmouseover="topMenuHover();">
				<a href="<?php echo $menuItem->getLink(); ?>" <?php echo $target; ?> 
					class="l1_link">
					<span class="<?php echo $spanClass;?>"><?php echo $menuItem->getMenuText();?></span>
				</a>
<?php 

				if (!empty($subItems)) {

?>
				<ul class="l2">	
<?php				
					foreach ($subItems as $subMenu) {

						$targetVal = $subMenu->getTarget();
						$target = empty($targetVal) ? '' : ' target= "' . $targetVal . '" ';

						$subSubItems = $subMenu->getSubMenuItems();
						$link = $subMenu->getLink();
						if (empty($target)) {
							$target = (strstr($link, 'CentralController') === false) ? '' : ' target="rightMenu" ';
						}
						
						$class = empty($subSubItems) ? 'l2_link' : 'l2_link parent';
						if (!$subMenu->isEnabled()) {
							if (!empty($class)) {
								$class .= ' ';
							}
							$class .= 'disabled';
							$link = '#';
						} else {
							$link = $subMenu->getLink();								
						}
						
						/* TODO: set subMenu->getIcon to id of <a> tag (after changing icon to id and passing unique id's to the menu)*/
						if (!empty($class)) {
						    $class .= ' ';
						}
						$class .= $subMenu->getIcon();
?>
						<li class="l2">
							<a href="<?php echo $link; ?>" <?php echo $target; ?> class="<?php echo $class;?>" onclick="menuclicked(this);" >
								<span><?php echo $subMenu->getMenuText(); ?></span>
							</a>
<?php					
						
						if (!empty($subSubItems)) {
?>
						<ul class="l3">
<?php							

							foreach($subSubItems as $subSub) {
								$link = $subSub->getLink();

                                $openInRight = (strstr($link, 'CentralController') !== false) || (strstr($link, 'symfony') !== false);

								$target = $openInRight ? 'target="rightMenu"' : '';
								if (!$subSub->isEnabled()) {
									$class = 'disabled';
									$link = '#';
								} else {
									$class = "";
									$link = $subSub->getLink();
								}
								
								if (!empty($class)) {
								    $class .= ' ';
								}
								$class .= $subSub->getIcon();
								
?>
						<li class="l3">
							<a href="<?php echo $link; ?>" onclick="menuclicked(this);" class="<?php echo $class; ?>" <?php echo $target; ?> >
								<span><?php echo $subSub->getMenuText();?></span></a></li>
<?php
							}
?>

					</ul>
<?php							
						}					
?>
						</li>
<?php					
						
					}
?>

				</ul>
<?php										
				}
?>				
				</li>
<?php						
		}
	}	
?>
</ul>

</div>


<?php		
	}
	
	public function getMenuHeight() {
		return 40;
	}
	
	public function getMenuWidth() {
		return 0;
	}

	
}
?>


