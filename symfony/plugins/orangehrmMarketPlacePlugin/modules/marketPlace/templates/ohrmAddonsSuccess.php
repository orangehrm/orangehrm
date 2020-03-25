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
    <div class="inner" id="addon_div">
        <div class="message success" id="messege_div">
            <ol>
                <li id="message_body"></li>
            </ol>
            <a href="#" class="messageCloseButton"><?php echo __('Close'); ?></a>
        </div>
        <?php
        $buyNowPendingAddon = $sf_data->getRaw("buyNowPendingAddon");
        $paidAddons = $sf_data->getRaw("paidAddonIds");
        $installedAddons = $sf_data->getRaw("installedAddons");
        $expiredAddons = $sf_data->getRaw("expiredAddons");
        $renewPendingAddons = $sf_data->getRaw("renewPendingAddons");
        $renewedAddons = $sf_data->getRaw("renewedAddonIds");
        $expirationDates = $sf_data->getRaw("expirationDates");
        $updatePendingAddons = $sf_data->getRaw("updatePendingAddons");
        $paidTypeAddonIds = $sf_data->getRaw("paidTypeAddonIds");
        if (!$exception) {
        if ($canRead) { ?>
        <?php foreach ($addonList as $addon) { ?>
            <div class="row">
                <div class="inner container" id="addonHolder">
                    <div class="accordion" addonid="<?php echo $addon['id']; ?>">
                        <div id="column" class="image">
                            <img class="circle" src="<?php echo $addon['icon']; ?>"/>
                        </div>
                        <div id="column" class="details">
                            <div class="row">
                                <label id="title"><?php echo __($addon['title']); ?></label>
                            </div>
                            <div class="row">
                                <p><?php echo $addon['summary']; ?></p>
                            </div>
                            <div class="row">
                                <p><?php
                                    if (count($addon['prerequisites']) != 0 || $addon['type'] == "paid") {
                                        echo "Prerequisites:- ";
                                        if ($addon['type'] == "paid" && count($addon['prerequisites']) != 0) {
                                            echo "ionCube Loader, ";
                                        }
                                        if ($addon['type'] == "paid" && count($addon['prerequisites']) == 0) {
                                            echo "ionCube Loader";
                                        }
                                        foreach ($addon['prerequisites'] as $prerequisite) {
                                            if ($prerequisite['type'] == 'php_extension') {
                                                echo $prerequisite['params']['extension'];
                                                break;
                                            }
                                        }

                                    }
                                    ?></p>
                            </div>
                            <div>
                                <p><?php
                                    if (array_key_exists($addon['id'], $expirationDates)) {
                                        echo __('Expiry Date') . ':- ' .$expirationDates[$addon['id']];
                                    }
                                ?></p>
                            </div>
                        </div>
                        <div id="column" class="install_button">
                            <?php
                            if (in_array($addon['id'], $installedAddons)) {
                                if ($canDelete) {
                                    if (in_array($addon['id'], $updatePendingAddons)) { ?>
                                        <input type="button" name="Submit" class="buttons updateBtn"
                                               id="<?php echo 'updateButton' . $addon['id']; ?>"
                                               value="<?php echo __('Update'); ?>"
                                               addid=<?php echo $addon['id'] ?>
                                        >
                                    <?php }
                                    if (!in_array($addon['id'], $updatePendingAddons) || $addon['type'] != 'paid') {?>
                                    <input type="button" name="Submit"
                                           class="buttons delete uninstallBtn <?php if ($addon['type'] == 'paid') {
                                               echo 'requested';
                                           } ?>"
                                           id="<?php echo 'uninstallButton' . $addon['id']; ?>"
                                           value="<?php
                                           if ($addon['type'] == 'paid') {
                                               echo __('Installed');
                                           } else {
                                               echo __('Uninstall');
                                           } ?>" data-toggle="modal"
                                           data-target="#deleteConfModal"
                                           <?php if ($addon['type'] == 'paid') {
                                                echo 'disabled';
                                           } ?>
                                           addid=<?php echo $addon['id'] ?>>
                                    <?php }
                                }
                            } elseif ($canCreate and ($addon['type'] == 'free') || in_array($addon['id'], $paidAddons)) { ?>
                                <input type="button" name="Submit" class="buttons installBtn"
                                       id="<?php echo 'installButton' . $addon['id']; ?>"
                                       value="<?php echo __('Install'); ?>"
                                       addid=<?php echo $addon['id'] ?>
                                > <?php }
                            elseif ($canCreate and ($addon['type'] == 'paid') and in_array($addon['id'], $expiredAddons)) { ?>
                                <input type="button" name="Submit" class="buttons requestRenewBtn"
                                       id="<?php echo 'requestRenewButton' . $addon['id']; ?>"
                                       value="<?php echo __('Request Renew'); ?>"
                                       addid=<?php echo $addon['id'] ?>
                                       isRenew = <?php echo true; ?>
                                >
                                <input type="button" name="Submit"
                                       class="buttons delete uninstallBtn"
                                       id="<?php echo 'uninstallButton' . $addon['id']; ?>"
                                       value="<?php echo __('Uninstall'); ?>"
                                       data-toggle="modal"
                                       data-target="#deleteConfModal"
                                       addid=<?php echo $addon['id'] ?>
                                >
                                <?php }
                            elseif ($canCreate and ($addon['type'] == 'paid') and in_array($addon['id'], $renewedAddons)) { ?>
                                <input type="button" name="Submit" class="buttons renewBtn"
                                       id="<?php echo 'renewButton' . $addon['id']; ?>"
                                       value="<?php echo __('Renew'); ?>"
                                       addid=<?php echo $addon['id'] ?>
                                > <?php }
                            elseif ($canCreate and $addon['type'] == 'paid') { ?>
                                <input type="button" name="Submit"
                                       class="buttons buyBtn <?php if (in_array($addon['id'], $buyNowPendingAddon) || in_array($addon['id'], $renewPendingAddons)) {
                                           echo 'requested';
                                       } ?>"
                                       id="<?php echo 'buyBtn' . $addon['id']; ?>"
                                       value="<?php if (in_array($addon['id'], $buyNowPendingAddon)) {
                                           echo __('Requested');
                                       } elseif (in_array($addon['id'], $renewPendingAddons)) {
                                           echo __('Renew Requested');
                                       } else {
                                           echo __('Request');
                                       } ?>"
                                       <?php if (in_array($addon['id'], $buyNowPendingAddon) || in_array($addon['id'], $renewPendingAddons)) {
                                           echo 'disabled';
                                       } ?>
                                       addid=<?php echo $addon['id'] ?>
                                       isRenew = <?php echo false; ?>
                                > <?php } ?>
                                <?php if (in_array($addon['id'], $renewPendingAddons)) :?>
                                    <input type="button" name="Submit"
                                           class="buttons delete uninstallBtn"
                                           id="<?php echo 'uninstallButton' . $addon['id']; ?>"
                                           value="<?php echo __('Uninstall'); ?>"
                                           data-toggle="modal"
                                           data-target="#deleteConfModal"
                                           addid=<?php echo $addon['id'] ?>
                                    >
                                <?php endif; ?>
                        </div>
                    </div>
                    <div id="<?php echo 'des' . $addon['id'] ?>" class="panel"><?php echo $addon->getRaw('description');?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
    <?php }
    } else {
        echo "<p id='errMessage'>$errorMessage</p>";
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
<!--install add_on confirmation modal-->
<div class="modal hide" id="updateConfModal">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3><?php echo __('OrangeHRM - Confirmation Required'); ?></h3>
    </div>
    <div class="modal-body">
        <p><?php echo __("Are you sure you want to update the selected addon?"); ?></p>
    </div>
    <div class="modal-footer">
        <input type="button" class="btn" data-dismiss="modal" id="modal_confirm_update"
               value="<?php echo __('Confirm'); ?>"/>
        <input type="button" class="btn cancel" data-dismiss="modal" value="<?php echo __('Cancel'); ?>"/>
    </div>
</div>
<!--Prerequisites Not Met Modal-->
<div class="modal hide" id="prerequisitesNotMetModal">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3><?php echo __('OrangeHRM - Prerequisites Verification'); ?></h3>
    </div>
    <div class="modal-body">
        <div id="prerequisitesNotMet" class="box"></div>
    </div>
    <div class="modal-footer">
        <input type="button" class="btn" data-dismiss="modal" value="<?php echo __('Ok'); ?>"/>
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
        <p><?php echo __("Please confirm your contact details. Your details will be forwarded to OrangeHRM sales representative."); ?></p>
        <div class="box">
            <form id="frmBuyNow" method="post">
                <ol>
                    <?php echo $buyNowForm->render() ?>
                </ol>
            </form>
        </div>
        <div id="prerequisites" class="box"></div>

    </div>
    <div class="modal-footer">
        <input type="button" class="btn" id="modal_confirm_buy"
               value="<?php echo __('Ok'); ?>"/>
        <input type="button" class="btn cancel" data-dismiss="modal" value="<?php echo __('Cancel'); ?>"/>
    </div>

</div>
<!--renew confirmation modal-->
<div class="modal hide" id="renewModal">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3><?php echo __('Renew the addon license'); ?></h3>
    </div>
    <div class="modal-body">
        <p><?php echo __("You are requesting to renew this add-on."); ?></p><br>
        <p><?php echo __("Please confirm your contact details. Your details will be forwarded to OrangeHRM sales representative."); ?></p>
        <div class="box">
            <form id="frmRenewNow" method="post">
                <ol>
                    <?php echo $buyNowForm->render() ?>
                </ol>
            </form>
        </div>

    </div>
    <div class="modal-footer">
        <input type="button" class="btn" id="modal_confirm_renew"
               value="<?php echo __('Ok'); ?>"/>
        <input type="button" class="btn cancel" data-dismiss="modal" value="<?php echo __('Cancel'); ?>"/>
    </div>
</div>

<script>
    var marketplaceURL = "<?php echo url_for('marketPlace/ohrmAddons'); ?>";
    //var descriptionUrl = "<?php echo url_for('marketPlace/getAddonDescriptionAPI'); ?>";
    var installUrl = "<?php echo url_for('marketPlace/installAddonAPI'); ?>";
    var updateUrl = "<?php echo url_for('marketPlace/updateAddonAPI'); ?>";
    var uninstallUrl = "<?php echo url_for('marketPlace/uninstallAddonAPI'); ?>";
    var buyNowUrl = "<?php echo url_for('marketPlace/ohrmBuyNowAPI'); ?>";
    var prerequisiteVerificationUrl = "<?php echo url_for('marketPlace/prerequisiteVerification');?>";
    var paidAddons = <?php echo json_encode($paidAddons);?>;
    var renewUrl = "<?php echo url_for('marketPlace/renewAPI');?>";
    var paidTypeAddonIds = <?php echo json_encode($paidTypeAddonIds); ?>;

    var meassageInSuccess = "<?php echo __js('Successfully Installed'); ?>";
    var meassageInUpdateSuccess = "<?php echo __js('Successfully Updated'); ?>";
    var messaegeInFail = "<?php echo __js('Failed to Install'); ?>";
    var messaegeInUpdateFail = "<?php echo __js('Failed to Update'); ?>";
    var meassageUninSuccess = "<?php echo __js('Successfully Uninstalled'); ?>";
    var meassageUninFail = "<?php echo __js('Failed to Uninstall'); ?>";
    var buyNowReqSuccess = "<?php echo __js('Your request has been forwarded'); ?>";
    var buyNowReqFail = "<?php echo __js('Failed to proceed with the request, try again.'); ?>";
    var renewSuccess = "<?php echo __js('Successfully Renewed license.'); ?>";
    var renewFail = "<?php echo __js('Renew Failed.'); ?>";

    var networkErrMessage = "<?php echo __js('Please connect to the internet to view the available add-ons.'); ?>";
    var marketpalceErrMessage = "<?php echo __js('Error Occur Please try again later'); ?>";
    var installErrorMessage = {
        "e3000": "<?php echo __js('3000 : Please connect to the internet to view the available add-ons. '); ?>",
        "e1001": "<?php echo __js('1001: Running php symfony cc fails. '); ?>",
        "e1002": "<?php echo __js('1002: Failed to execute database queries.'); ?>",
        "e1004": "<?php echo __js('1004: Running php symfony o:publish-asset fails. '); ?>",
        "e1005": "<?php echo __js('1005: Running php symfony d:build-model fails. '); ?>",
        "e1006": "<?php echo __js('1006: Can not add to OrangeHRM database. Uninstallation will cause errors. But plugin can used. '); ?>",
        "e1007": "<?php echo __js('1007: Downloaded file corrupted.'); ?>",
        "e1008": "<?php echo __js('1008: Error when retrieving the license file.'); ?>",
        "e1009": "<?php echo __js('1009: Error when renewing the license file.'); ?>"
    };
    var uninstallErrorMessage = {
        "e2000": "<?php echo __js('2000: Selected plugin to uninstall is not tracked in database. '); ?>",
        "e2001": "<?php echo __js('2001: Uninstall file execution fails. '); ?>",
        "e2002": "<?php echo __js('2002: Removing plugin folder fails. '); ?>",
        "e2003": "<?php echo __js('2003: Running php symfony cc fails. '); ?>",
        "e2004": "<?php echo __js('2004: Running php symfony o:publish-asset fails. '); ?>",
        "e2005": "<?php echo __js('2005: Running php symfony d:build-model fails. '); ?>"
    };

    var emailRequired = "<?php echo __js('Required'); ?>";
    var emailValidation = "<?php echo __js('Enter a valid email'); ?>";
    var contactRequired = "<?php echo __js('Required'); ?>";
    var contactValidation = "<?php echo __js('Enter a valid contact number'); ?>";
    var organizationRequired = "<?php echo __js('Required'); ?>";
    var organizationValidation = "<?php echo __js('Organization max length exceded'); ?>";
</script>
