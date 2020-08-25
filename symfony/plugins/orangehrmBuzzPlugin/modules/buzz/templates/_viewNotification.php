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

use_stylesheet(plugin_web_path('orangehrmBuzzPlugin', 'css/viewNotificationComponent'));
use_stylesheet(plugin_web_path('orangehrmBuzzPlugin', 'css/viewShareSuccess'));
use_stylesheet(plugin_web_path('orangehrmBuzzPlugin', 'css/viewBuzzSuccess'));
use_stylesheet(plugin_web_path('orangehrmBuzzPlugin', 'css/getSharedEmployeeListSuccess'));
use_stylesheet(plugin_web_path('orangehrmBuzzPlugin', 'css/viewPostComponent'));
use_stylesheet(plugin_web_path('orangehrmBuzzPlugin', 'css/photoTiling'));
use_stylesheet(plugin_web_path('orangehrmBuzzPlugin', 'css/messageBoxStyles'));
use_javascript(plugin_web_path('orangehrmBuzzPlugin', 'js/viewNotificationComponent'));
use_javascript(plugin_web_path('orangehrmBuzzPlugin', 'js/buzzCommon'));
use_javascript(plugin_web_path('orangehrmBuzzPlugin', 'js/buzzNew'));
use_javascript(plugin_web_path('orangehrmBuzzPlugin', 'js/viewPostComponent'));
?>

<!--Notification icon-->
<div class="notification">
    <span id="notification" class="fa-lg fa-layers fa-fw">
        <i class="fas fa-bell notification-icon"></i>
        <span id="notificationBadge"
              class="fa-layers-counter notification-badge <?php if ($batchHide) echo 'hide-notification-badge'; ?>"></span>
    </span>
</div>

<!-- Modal -->
<div class="modal hide" id="notificationModal" role="dialog" data-backdrop="false">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-body">
                <div class="clear-notifications">
                    <?php if (!$empty) { ?>
                        <a id="clearNotificationsLink" class="clear-notifications-link">
                            <?php echo __("Clear all notifications"); ?>
                        </a>
                    <?php } ?>
                </div>
                <div id="notificationsMessages"
                     class="notifications-message <?php if ($empty) echo 'empty-notifications-message'; ?>"><?php if ($empty) echo __('No new notifications'); ?></div>
                <div class="<?php if (!$empty) echo 'notification-container'; ?>">
                    <?php foreach ($notifications as $key => $notification) { ?>

                        <div class="notification-row <?php if (strtotime($notification['time']) > strtotime($lastNotificationViewTime)) echo 'notification-new-row'; ?>"
                             id="notification_<?php echo $key; ?>"
                             data-shareid="<?php echo $notification["shareId"]; ?>">

                            <div class="notification-pic-name-container">
                                <div id="profilePicContainer" class="notification-profile-pic-container">
                                    <img
                                         alt="<?php echo __("Employee Photo"); ?>"
                                         src="<?php echo url_for("buzz/viewPhoto?empNumber=" . $notification["empNumber"]); ?>"/>
                                </div>
                                <div class="notification-message" title="<?php echo $notification["message"]; ?>">
                                    <?php echo $notification["message"]; ?>
                                </div>
                                <br>
                                <div class="notification-elapsed-time">
                                    <?php echo $notification["elapsedTime"]; ?>
                                </div>
                            </div>
                        </div>

                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <div>
        <form id="actionValidateForm" method="POST" action=""
              enctype="multipart/form-data">
            <fieldset>
                <ol>
                    <?php echo $actionValidateForm->render(); ?>
                </ol>
            </fieldset>

        </form>

        <form id="likedOrSharedEmployeeForm" method="POST" action="" >
            <?php echo $likedOrSharedEmployeeForm->render(); ?>
        </form>

        <form id="deleteOrEditShareForm" method="POST" action="" >
            <?php echo $deleteOrEditShareForm->render(); ?>
        </form>
    </div>
</div>

<!-- pop up-->
<div class="modal hide notification-view-more-modal" id="notificationShareViewMoreModal">
    <div class="modal-body notification-view-more-modal-body">
        <div class="notification-hide-modal-popup">
            <img
                    class="notification-hide-modal-popup"
                    src="<?php echo plugin_web_path("orangehrmBuzzPlugin", "images/close.png"); ?>"
                    height="20"
                    width="20"
            /></div>

        <div id="notificationShareView">
            <div class="shareView"></div>
        </div>

    </div>
</div>
<div class="modal hide" id="successDataModal" >

    <div class="modal-body" >
        <div class="mb-heading ac_over">
            <?php echo __("Success") . "!"; ?>
        </div>
        <div id="successBodyEdit" >
            <?php echo __("Successfully Saved"); ?>
        </div>
        <div id="successBodyShare" >
            <?php echo __("Successfully Shared"); ?>
        </div>
        <div id="successBodyDelete" >
            <?php echo __("Successfully Deleted"); ?>
        </div>

    </div>
</div>

<!--start loading window popup window-->
<div class="modal hide" id="loadingDataModal" >
    <div class="modal-body loadingDataModal-body" >
        <div id="loadingModalBody" >
            <img id="img-spinner-loading"   src="<?php echo plugin_web_path("orangehrmBuzzPlugin", "images/vload.gif"); ?>"
                 height="12"  />
        </div>
    </div>
</div>
<!--end loading window pop up window-->

<!-- start like window popup window-->
<div class="modal hide modal-on-preview" id='<?php echo 'postsharehide' ?>'>
    <div id="modalHeader" >
        <?php echo __("People who shared this post"); ?>
    </div>
    <div class="modal-body originalPostModal-body" >
        <div class="hideModalPopUp" id='<?php echo 'postsharehide' ?>'>
            <img
                    class="hideModalPopUp" id='<?php echo 'postsharehide' ?>'
                    src="<?php echo plugin_web_path("orangehrmBuzzPlugin", "images/close.png"); ?>" height="20" width="20"
            /></div>
        <div class=""  id='<?php echo 'postsharehidebody' ?>'></div>

    </div>
</div>
<!-- end like window pop up window-->

<!-- start like window popup window-->
<div class="modal hide modal-on-preview" id='<?php echo 'postlikehide' ?>'>
    <div id="modalHeader" >
        <?php echo __("People who like this post"); ?>
    </div>
    <div class="modal-body originalPostModal-body" >
        <div class="hideModalPopUp" id='<?php echo 'postlikehide' ?>'>
            <img
                    class="hideModalPopUp" id='<?php echo 'postlikehide' ?>'
                    src="<?php echo plugin_web_path("orangehrmBuzzPlugin", "images/close.png"); ?>" height="20" width="20"
            /></div>
        <div class=""  id='<?php echo 'postlikehidebody' ?>'></div>

    </div>
</div>
<!-- end like window pop up window-->

<script>
    var getAccessUrl = '<?php echo url_for('buzz/getLogedToBuzz'); ?>';
    var loginpageURL = '<?php echo url_for('auth/login'); ?>';

    // buzzCommon.js
    var viewLikedEmployees = '<?php echo url_for('buzz/viewLikedEmployees'); ?>';
    var addBuzzCommentURL = '<?php echo url_for('buzz/addNewComment'); ?>';
    var shareShareURL = '<?php echo url_for('buzz/shareAPost'); ?>';
    var getLikedEmployeeListURL = '<?php echo url_for('buzz/getLikedEmployeeList'); ?>';

    // buzzNew.js
    var shareLikeURL = '<?php echo url_for('buzz/likeOnShare'); ?>';
    var commentLikeURL = '<?php echo url_for('buzz/likeOnComment'); ?>';
    var getSharedEmployeeListURL = '<?php echo url_for('buzz/getSharedEmployeeList'); ?>';

    // viewNotificationComponent.js
    var viewMoreShare = '<?php echo url_for('buzz/viewShare'); ?>';
    var buzzURL = "<?php echo url_for('buzz/viewBuzz'); ?>";
    var ClearNotificationURL = "<?php echo url_for('buzz/clearNotificationAjax'); ?>";
    var ClickOnNotificationIconURL = "<?php echo url_for('buzz/clickOnNotificationIconAjax'); ?>";
    var lang_NoNewNotifications = '<?php echo __js("No new notifications");?>';
    var lang_NotificationClearFailed = '<?php echo __js("Failed to clear notifications");?>';
</script>
