<?php if ($notificationEnabled) { 
     use_stylesheet(plugin_web_path('orangehrmBeaconPlugin', 'css/_beaconNotification.css'));
    ?>

<div class="notification-bar" id = "notificationBar" >
        <div class = "notification-header" id="notificationHeader" >
          <h3>  <?php echo  html_entity_decode($notificationHeader); ?></h3>
    </div>
        <div class="notification-body" id = "notificationBody">
            <?php echo html_entity_decode($notificationBody); ?>
        </div>
    </div>
    <?php
}?>