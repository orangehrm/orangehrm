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
 * Boston, MA 02110-1301, USA
 */
use_stylesheet(plugin_web_path('orangehrmMarketPlacePlugin', 'css/ohrmAddonSuccess.css'));
use_javascript(plugin_web_path('orangehrmMarketPlacePlugin', 'js/ohrmAddonSuccess.js'));
?>
<div class="box">
    <div class="head">
        <h1 id="menu">OrangeHRM Addons</h1>
    </div>
    <div class="inner">
        <?php if ($isNetwork) { ?>
        <?php if ($canRead) { ?>
        <?php foreach ($addonList as $addon) { ?>
            <div class="row">
                <div class="inner container" id="addonHolder">
                    <button class="accordion" addOnId="<?php echo $addon['id']; ?>">
                        <div id="column" class="image">
                            <img class="circle" src="
                        <?php echo $addon['icon']; ?>"/>
                        </div>
                        <div id="column" class="details">
                            <div class="row">
                                <label id="title"><?php echo __($addon['title']); ?></label>
                            </div>
                            <div class="row">
                                <p><?php echo __($addon['summary']); ?></p>
                            </div>
                        </div>
                        <div id="column" class="install_button">
                            <?php $installedAddons = $sf_data->getRaw("installedAddons");
                            if (in_array($addon['id'], $installedAddons) and $canDelete) { ?>
                                <input type="button" name="Submit" class="delete" id="btn1"
                                       value="Uninstall"/> <?php } ?>
                            <?php if (!in_array($addon['id'], $installedAddons) and $canCreate) { ?>
                                <input type="button" name="Submit" class="" id="btn1" value="Install"/> <?php } ?>
                        </div>
                    </button>
                    <div class="panel">
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
    <?php }
    } else {
        echo "<p>$errorMessage</p>";
    } ?>
</div>
<script>
    var ajaxUrl = "<?php echo url_for('marketPlace/getAddonDescriptionAPI'); ?>";
</script>

