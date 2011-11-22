<link href="<?php echo public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css') ?>" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.core.js') ?>"></script>

<?php use_stylesheet('../orangehrmAdminPlugin/css/payGradeSuccess'); ?>
<?php use_javascript('../orangehrmAdminPlugin/js/payGradeSuccess'); ?>
<?php echo isset($templateMessage) ? templateMessage($templateMessage) : ''; ?>
<div id="messagebar" class="<?php echo isset($messageType) ? "messageBalloon_{$messageType}" : ''; ?>" >
	<span><?php echo isset($message) ? $message : ''; ?></span>
</div>

<div id="payGrade">
     <div class="outerbox">

         <div class="mainHeading"><h2 id="payGradeHeading"><?php echo __("Add Pay Grade"); ?></h2></div>
            <form name="frmPayGrade" id="frmPayGrade" method="post" action="<?php echo url_for('admin/payGrade'); ?>" >

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
	<div class="paddingLeftRequired"><?php echo __('Fields marked with an asterisk') ?> <span class="required">*</span> <?php echo __('are required.') ?></div>
</div>

<script type="text/javascript">
	var lang_NameRequired = "<?php echo __("Pay Grade name is required"); ?>";
	var lang_exceed50Charactors = "<?php echo __("Cannot exceed 50 charactors"); ?>";
	var payGradeId = "<?php echo $payGradeId; ?>";
	var lang_edit = "<?php echo __("Edit"); ?>";
	var lang_save = "<?php echo __("Save"); ?>";
	var lang_editPayGrade = "<?php echo __("Edit Pay Grade"); ?>";
	var lang_addPayGrade = "<?php echo __("Add Pay Grade"); ?>";
	var viewPayGradesUrl = "<?php echo url_for("admin/viewPayGrades"); ?>";
</script>