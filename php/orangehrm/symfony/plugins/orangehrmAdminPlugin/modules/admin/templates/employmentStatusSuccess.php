<link href="<?php echo public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css') ?>" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.core.js') ?>"></script>

<?php use_stylesheet('../orangehrmAdminPlugin/css/employmentStatusSuccess'); ?>
<?php use_javascript('../orangehrmAdminPlugin/js/employmentStatusSuccess'); ?>
<?php echo isset($templateMessage) ? templateMessage($templateMessage) : ''; ?>
<div id="messagebar" class="<?php echo isset($messageType) ? "messageBalloon_{$messageType}" : ''; ?>" >
	<span><?php echo isset($message) ? $message : ''; ?></span>
</div>

<div id="empStatus">
    <div class="outerbox">

        <div class="mainHeading"><h2 id="empStatusHeading"><?php echo __("Add Employment Status"); ?></h2></div>
        <form name="frmEmpStatus" id="frmEmpStatus" method="post" action="<?php echo url_for('admin/employmentStatus'); ?>" >

            <?php echo $form['_csrf_token']; ?>
            <?php echo $form->renderHiddenFields(); ?>
            <br class="clear"/>
	    
	    <div class="newColumn">
                <?php echo $form['name']->renderLabel(__('Name'). ' <span class="required">*</span>'); ?>
                <?php echo $form['name']->render(array("class" => "formInput", "maxlength" => 52)); ?>
                <div class="errorHolder"></div>
            </div>
	    <br class="clear"/>
	    
	    <div class="formbuttons">
                    <input type="button" class="savebutton" name="btnSave" id="btnSave"
                           value="<?php echo __("Save"); ?>"onmouseover="moverButton(this);" onmouseout="moutButton(this);"/>
                    <input type="button" class="cancelbutton" name="btnCancel" id="btnCancel"
                           value="<?php echo __("Cancel"); ?>"onmouseover="moverButton(this);" onmouseout="moutButton(this);"/>
	    </div>
	    
	</form>
    </div>
</div>
<div class="paddingLeftRequired"><?php echo __('Fields marked with an asterisk') ?> <span class="required">*</span> <?php echo __('are required.') ?></div>
<script type="text/javascript">
	var lang_NameRequired = "<?php echo __("Status name is required"); ?>";
	var lang_exceed50Charactors = "<?php echo __("Cannot exceed 50 charactors"); ?>";
</script>