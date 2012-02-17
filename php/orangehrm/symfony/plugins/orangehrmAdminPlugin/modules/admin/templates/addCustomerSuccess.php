<link href="<?php echo public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css') ?>" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.core.js') ?>"></script>

<?php use_stylesheet('../orangehrmAdminPlugin/css/addCustomerSuccess'); ?>
<?php use_javascript('../orangehrmAdminPlugin/js/addCustomerSuccess'); ?>
<?php echo isset($templateMessage) ? templateMessage($templateMessage) : ''; ?>
<div id="messagebar" class="<?php echo isset($messageType) ? "messageBalloon_{$messageType}" : ''; ?>" >
	<span><?php echo isset($message) ? $message : ''; ?></span>
</div>

<div id="addCustomer">
            <div class="outerbox">

                <div class="mainHeading"><h2 id="addCustomerHeading"><?php echo __("Add Customer"); ?></h2></div>
                <form name="frmAddCustomer" id="frmAddCustomer" method="post" action="<?php echo url_for('admin/addCustomer'); ?>" >

            <?php echo $form['_csrf_token']; ?>
            <?php echo $form->renderHiddenFields(); ?>
            <br class="clear"/>
	    <div class="newColumn">
                <?php echo $form['customerName']->renderLabel(__('Name'). ' <span class="required">*</span>'); ?>
                <?php echo $form['customerName']->render(array("class" => "formInput", "maxlength" => 52)); ?>
                <div class="errorHolder"></div>
            </div>
	    <br class="clear"/>
	    
	    <div class="newColumn">
                <?php echo $form['description']->renderLabel(__('Description')); ?>
                <?php echo $form['description']->render(array("class" => "formInput", "maxlength" => 255)); ?>
                <div class="errorHolder"></div>
            </div>
	    <br class="clear"/>
	    
	    
	    <div class="formbuttons">
                    <input type="button" class="savebutton" name="btnSave" id="btnSave"
                           value="<?php echo __("Save"); ?>"onmouseover="moverButton(this);" onmouseout="moutButton(this);"/>
                    <input type="button" class="cancelbutton" name="btnCancel" id="btnCancel"
                           value="<?php echo __("Cancel"); ?>"onmouseover="moverButton(this);" onmouseout="moutButton(this);"/>
	    </div>
	    </div>
    </form>
</div>
<div class="paddingLeftRequired"><span class="required">*</span> <?php echo __(CommonMessages::REQUIRED_FIELD); ?></div>
<script type="text/javascript">
	var customers = <?php echo str_replace('&#039;', "'", $form->getCustomerListAsJson()) ?> ;
        var customerList = eval(customers);
	var lang_customerNameRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
	var lang_exceed50Charactors = '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 50)); ?>';
	var lang_exceed255Charactors = '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 250)); ?>';
	var lang_editCustomer = "<?php echo __("Edit Customer"); ?>";
	var lang_uniqueName = '<?php echo __(ValidationMessages::ALREADY_EXISTS); ?>';
	var lang_edit = "<?php echo __("Edit"); ?>";
	var lang_save = "<?php echo __("Save"); ?>";
	var customerId = '<?php echo $customerId;?>';
	var cancelBtnUrl = '<?php echo url_for('admin/viewCustomers'); ?>';
</script>