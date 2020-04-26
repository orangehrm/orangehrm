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
use_javascript(plugin_web_path('orangehrmBuzzPlugin', 'js/viewNotificationComponent'));
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
                             data-shareid="<?php echo $notification["shareId"]; ?>"
                             data-href="<?php echo url_for("buzz/viewProfile?empNumber=" . $notification["postOwnerEmpNumber"]) . '?postId=' . $notification["shareId"]; ?>">

                            <div class="picAndNameContainer">
                                <div id="profilePicContainer">
                                    <img class="profPic"
                                         alt="<?php echo __("Employee Photo"); ?>"
                                         src="<?php echo url_for("buzz/viewPhoto?empNumber=" . $notification["empNumber"]); ?>"/>
                                </div>
                                <div>
                                    <?php //if ($postSharerDeleted) { ?>
                                    <?php //echo $postEmployeeName; ?>
                                    <?php //} else { ?>
                                    <?php //echo $employee['emp_firstname'] . " " . $postEmployeeName; ?>
                                    <?php //} ?>
                                    <?php echo $notification["message"]; ?>
                                </div>
                                <br>
                                <div>
                                    <?php echo $notification["elapsedTime"]; ?>
                                </div>
                            </div>
                        </div>

                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const BuzzURL = "<?php echo url_for('buzz/viewBuzz'); ?>";
    const ClearNotificationURL = "<?php echo url_for('buzz/clearNotificationAjax'); ?>";
    const ClickOnNotificationIconURL = "<?php echo url_for('buzz/clickOnNotificationIconAjax'); ?>";
    const lang_NoNewNotifications = '<?php echo __js("No new notifications");?>';
    const lang_NotificationClearFailed = '<?php echo __js("Failed to clear notifications");?>';
</script>
