<?php
/*
 * OrangeHRM Enterprise is a closed sourced comprehensive Human Resource Management (HRM)
 * System that captures all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM Inc is the owner of the patent, copyright, trade secrets, trademarks and any
 * other intellectual property rights which subsist in the Licensed Materials. OrangeHRM Inc
 * is the owner of the media / downloaded OrangeHRM Enterprise software files on which the
 * Licensed Materials are received. Title to the Licensed Materials and media shall remain
 * vested in OrangeHRM Inc. For the avoidance of doubt title and all intellectual property
 * rights to any design, new software, new protocol, new interface, enhancement, update,
 * derivative works, revised screen text or any other items that OrangeHRM Inc creates for
 * Customer shall remain vested in OrangeHRM Inc. Any rights not expressly granted herein are
 * reserved to OrangeHRM Inc.
 *
 * You should have received a copy of the OrangeHRM Enterprise  proprietary license file along
 * with this program; if not, write to the OrangeHRM Inc. 538 Teal Plaza, Secaucus , NJ 0709
 * to get the file.
 *
 */
?>

<?php 
use_javascript(plugin_web_path('orangehrmAdminPlugin', 'js/emailNotificationSuccess')); 
?>

<div id="EmailNotificationList">
    <?php include_component('core', 'ohrmList', $parmetersForListCompoment); ?>
</div>


<!-- help messages are added after the list table printes -->

<ul id="helper_message" class="helpList">
    <li>
        * <?php echo __(' Click on a notification type to add subscribers') ?>
    </li>
    <li>
        * <?php echo __(' Click on Edit button to enable notifications') ?>
    </li>
</ul>


<script type="text/javascript">
    var notificationIds = <?php echo str_replace('&#039;', "'", $form->getEnabledNotificationIdListAsJson()) ?> ;
    var notificationIdList = eval(notificationIds);
	var lang_NameRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
	var lang_exceed50Charactors = '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 100)); ?>';
	var nationalityInfoUrl = "<?php echo url_for("admin/getNationalityJson?id="); ?>";
	var lang_editNationality = "<?php echo __("Edit Nationality"); ?>";
	var lang_addNationality = "<?php echo __("Add Nationality"); ?>";
	var lang_uniqueName = '<?php echo __(ValidationMessages::ALREADY_EXISTS); ?>';
</script>