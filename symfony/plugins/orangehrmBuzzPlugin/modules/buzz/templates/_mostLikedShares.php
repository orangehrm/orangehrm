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
use_stylesheet(plugin_web_path('orangehrmBuzzPlugin', 'css/mostLikedShares'));
use_stylesheet(plugin_web_path('orangehrmBuzzPlugin', 'css/buzzNew'));
use_stylesheet(plugin_web_path('orangehrmBuzzPlugin', 'css/viewBuzzSuccess'));
use_javascript(plugin_web_path('orangehrmBuzzPlugin', 'js/mostLikedShares'));
use_javascript(plugin_web_path('orangehrmBuzzPlugin', 'js/viewBuzzSuccess'));
?>
<div class ="rightBarBodyAll">
    <div class="rightBarHeading" id="rightBarHeadingMl"><?php echo __("MOST LIKED POSTS"); ?>
        <img id="morePostLiked" src="<?php echo plugin_web_path("orangehrmBuzzPlugin", "images/more2.png"); ?>" 
             height="30px" width="30px"/>
        <img id="lessPostLiked" src="<?php echo plugin_web_path("orangehrmBuzzPlugin", "images/less2.png"); ?>" 
             height="30px" width="30px"/>
    </div>
    <div id="ml_componentContainer" style="display:none;">
        <?php foreach ($result_ml_shares as $result) { ?>

            <?php include_component('buzz', 'viewPostPreview', array('post' => $result)); ?>

        <?php } ?>
    </div>
</div>
<div class ="rightBarBodyAll">
    <div class="rightBarHeading" id="rightBarHeadingMc"><?php echo __("MOST COMMENTED POSTS"); ?>
        <img id="moreCommentLiked" src="<?php echo plugin_web_path("orangehrmBuzzPlugin", "images/more2.png"); ?>" 
             height="30px" width="30px"/>
        <img id="lessCommentLiked" src="<?php echo plugin_web_path("orangehrmBuzzPlugin", "images/less2.png"); ?>" 
             height="30px" width="30px"/>
    </div>
    <div id="mc_componentContainer" style="display:none;">
        <?php foreach ($result_mc_shares as $resultMc) { ?>

            <?php include_component('buzz', 'viewPostPreview', array('post' => $resultMc)); ?>

        <?php } ?>
    </div>
</div>
