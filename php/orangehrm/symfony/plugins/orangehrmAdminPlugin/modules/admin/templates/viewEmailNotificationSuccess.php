<link href="<?php echo public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css') ?>" rel="stylesheet" type="text/css"/>

<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.core.js') ?>"></script>

<?php use_stylesheet('../../../themes/orange/css/ui-lightness/jquery-ui-1.8.13.custom.css'); ?>
<?php use_javascript('../../../scripts/jquery/ui/ui.core.js'); ?>
<?php use_javascript('../../../scripts/jquery/ui/ui.dialog.js'); ?>
<?php use_stylesheet('../orangehrmAdminPlugin/css/emailNotificationSuccess'); ?>
<?php use_javascript('../orangehrmAdminPlugin/js/emailNotificationSuccess'); ?>

<?php echo isset($templateMessage) ? templateMessage($templateMessage) : ''; ?>
<div id="messagebar" class="<?php echo isset($messageType) ? "messageBalloon_{$messageType}" : ''; ?>" >
	<span><?php echo isset($message) ? $message : ''; ?></span>
</div>
<div id="EmailNotificationList">
    <?php include_component('core', 'ohrmList', $parmetersForListCompoment); ?>
</div>

<div class="paddingLeftRequired"><?php echo __('Click on a notification type to add subscribers') ?></div>
<div class="paddingLeftRequired"><?php echo __('Click on Edit button to enable notifications') ?></div>

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