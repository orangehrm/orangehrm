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
use_javascript(plugin_web_path('orangehrmMarketPlacePlugin', 'js/ohrmAddonSuccessValidator.js'));
?>
<div class="box">
    <div class="head">
        <h1 id="menu"><?php echo __('OrangeHRM Addons'); ?></h1>
    </div>
    <div class="inner">
        <div class="message success" id="messege_div">
            <ol>
                <li id="message_body"></li>
            </ol>
            <a href="#" class="messageCloseButton"><?php echo __('Close'); ?></a>
        </div>
        <?php if ($isNetwork) {
        if ($canRead) { ?>
        <?php foreach ($addonList as $addon) { ?>
            <div class="row">
                <div class="inner container" id="addonHolder">
                    <button class="accordion" addonid="<?php echo $addon['id']; ?>">
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
                                <input type="button" name="Submit" class="buttons delete uninstallBtn"
                                       id="<?php echo 'uninstallButton' . $addon['id']; ?>"
                                       value="Uninstall" data-toggle="modal" data-target="#deleteConfModal"
                                       addid=<?php echo $addon['id'] ?>> <?php } ?>
                            <?php if (!in_array($addon['id'], $installedAddons) and $canCreate and $addon['type'] == 'free') { ?>
                                <input type="button" name="Submit" class="buttons installBtn"
                                       id="<?php echo 'installButton' . $addon['id']; ?>" value="Install"
                                       data-toggle="modal"
                                       data-target="#installConfModal" addid=<?php echo $addon['id'] ?>> <?php } ?>
                            <?php if (!in_array($addon['id'], $installedAddons) and $canCreate and $addon['type'] == 'paid') { ?>
                                <input type="button" name="Submit" class="buttons buyBtn"
                                       id="<?php echo 'buyBtn' . $addon['id']; ?>"
                                       value="<?php $buyNowPendingAddon = $sf_data->getRaw("buyNowPendingAddon");
                                       if (in_array($addon['id'], $buyNowPendingAddon)) {
                                           echo __('Requested');
                                       } else {
                                           echo __('Request');
                                       } ?>" <?php if (in_array($addon['id'], $buyNowPendingAddon)) {
                                    echo 'disabled';
                                } ?>
                                       data-toggle="modal"
                                       data-target="#buyNowModal"
                                       style="<?php if (in_array($addon['id'], $buyNowPendingAddon)) {
                                           echo 'background-color: #808080;';
                                       } ?>" addid=<?php echo $addon['id'] ?>> <?php } ?>
                        </div>
                    </button>
                    <div id="<?php echo 'des' . $addon['id'] ?>" class="panel">
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
<div id="disable-screen"></div>
<div id="loading"></div>
<!--delete confirmation modal-->
<div class="modal hide" id="deleteConfModal">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3><?php echo __('OrangeHRM - Confirmation Required'); ?></h3>
    </div>
    <div class="modal-body">
        <p><?php echo __("Are you sure you want to remove this app and all it's dependencies?"); ?></p>
    </div>
    <div class="modal-footer">
        <input type="button" class="btn" data-dismiss="modal" id="modal_confirm_uninstall"
               value="<?php echo __('Confirm'); ?>"/>
        <input type="button" class="btn cancel" data-dismiss="modal" value="<?php echo __('Cancel'); ?>"/>
    </div>
</div>
<!--install add_on confirmation modal-->
<div class="modal hide" id="installConfModal">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3><?php echo __('OrangeHRM - Confirmation Required'); ?></h3>
    </div>
    <div class="modal-body">
        <p><?php echo __("Are you sure you want to install this application and it's dependencies?"); ?></p>
    </div>
    <div class="modal-footer">
        <input type="button" class="btn" data-dismiss="modal" id="modal_confirm_install"
               value="<?php echo __('Confirm'); ?>"/>
        <input type="button" class="btn cancel" data-dismiss="modal" value="<?php echo __('Cancel'); ?>"/>
    </div>
</div>
<!--Buy now modal-->
<div class="modal hide" id="buyNowModal">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3><?php echo __('Request an add-on'); ?></h3>
    </div>
    <div class="modal-body">
        <p><?php echo __("You are requesting to buy this add-on."); ?></p><br>
        <p><?php echo __("Please confirm your contact details. Your details will be forwarded to sales@orangehrm.com."); ?></p>
        <div class="box">
            <form id="frmBuyNow" method="post">
                <ol>
                    <?php echo $buyNowForm->render() ?>
                </ol>
            </form>
        </div>
    </div>
    <div class="modal-footer">
        <input type="button" class="btn" id="modal_confirm_buy"
               value="<?php echo __('Ok'); ?>"/>
        <input type="button" class="btn cancel" data-dismiss="modal" value="<?php echo __('Cancel'); ?>"/>
    </div>

</div>
<script>
    var descriptionUrl = "<?php echo url_for('marketPlace/getAddonDescriptionAPI'); ?>";
    var installUrl = "<?php echo url_for('marketPlace/installAddonAPI'); ?>";
    var uninstallUrl = "<?php echo url_for('marketPlace/uninstallAddonAPI'); ?>";
    var buyNowUrl = "<?php echo url_for('marketPlace/ohrmBuyNowAPI'); ?>";

    var meassageInSuccess = "<?php echo __('Successfully Installed'); ?>";
    var messaegeInFail = "<?php echo __('Failed to Install'); ?>";
    var meassageUninSuccess = "<?php echo __('Successfully Uninstalled'); ?>";
    var meassageUninFail = "<?php echo __('Failed to Uninstall'); ?>";
    var buyNowReqSuccess = "<?php echo __('Your request has been forwarded'); ?>";
    var buyNowReqFail = "<?php echo __('Failed to proceed with the request, try again.'); ?>";

    var emailRequired = "<?php echo __('Required'); ?>";
    var emailValidation = "<?php echo __('Enter a valid email'); ?>";
    var contactRequired = "<?php echo __('Required'); ?>";
    var contactValidation = "<?php echo __('Enter a valid contact number'); ?>";
    var organizationRequired = "<?php echo __('Required'); ?>";
    var organizationValidation = "<?php echo __('Organization max length exceded'); ?>";
</script>
