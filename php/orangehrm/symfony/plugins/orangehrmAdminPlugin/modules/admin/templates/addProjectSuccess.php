<link href="<?php echo public_path('../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css') ?>" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.core.js') ?>"></script>

<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.draggable.js')?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.resizable.js')?>"></script>
<script type="text/javascript" src="<?php echo public_path('../../scripts/jquery/ui/ui.dialog.js')?>"></script>

<?php use_stylesheet('../orangehrmAdminPlugin/css/addProjectSuccess'); ?>
<?php use_javascript('../orangehrmAdminPlugin/js/addProjectSuccess'); ?>
<?php use_stylesheet('../../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css'); ?>
<?php use_javascript('../../../scripts/jquery/ui/ui.core.js'); ?>
<?php use_stylesheet('../../../themes/orange/css/jquery/jquery.autocomplete.css'); ?>
<?php use_javascript('../../../scripts/jquery/jquery.autocomplete.js'); ?>
<?php echo isset($templateMessage) ? templateMessage($templateMessage) : ''; ?>
<div id="messagebar" class="<?php echo isset($messageType) ? "messageBalloon_{$messageType}" : ''; ?>" >
	<span><?php echo isset($message) ? $message : ''; ?></span>
</div>

<div id="addProject">
            <div class="outerbox">

                <div class="mainHeading"><h2 id="addProjectHeading"><?php echo __("Project"); ?></h2></div>
                <form name="frmAddProject" id="frmAddProject" method="post" action="<?php echo url_for('admin/addProject'); ?>" >

            <?php echo $form['_csrf_token']; ?>
            <?php echo $form->renderHiddenFields(); ?>
            <br class="clear"/>

                <?php echo $form['customerName']->renderLabel(__('Customer Name'). ' <span class="required">*</span>'); ?>
                <?php echo $form['customerName']->render(array("class" => "formInputCustomer", "maxlength" => 52)); ?>	
		    <br class="clear"/>
		    <span id="addCustomerLink"><?php echo "<a href=\"javascript:openDialogue()\">".__('Add Customer')."</a>" ?></span>
		    <div class="errorHolder"></div>

	    <br class="clear"/>
	    

                <?php echo $form['projectName']->renderLabel(__('Name'). ' <span class="required">*</span>'); ?>
                <?php echo $form['projectName']->render(array("class" => "formInput", "maxlength" => 52)); ?>
                <div class="errorHolder"></div>

	    <br class="clear"/>
	    
	    <label class="firstLabel"><?php echo __('Project Admin'); ?></label>

            <?php for ($i = 1; $i <= $form->numberOfProjectAdmins; $i++) {
            ?>
                <div class="projectAdmin" id="<?php echo "projectAdmin_" . $i ?>">
                <?php echo $form['projectAdmin_' . $i]->render(array("class" => "formInputProjectAdmin", "maxlength" => 100)); ?>                
		<span class="removeText" id=<?php echo "removeButton" . $i ?>><?php echo __('Remove'); ?></span>	
		<div class="errorHolder projectAdminError"></div>
		<br class="clear" />
		
            </div>
            <?php } ?> 
	    <a class="addText" id='addButton'><?php echo __('Add Another'); ?></a> 
	    <div id="projectAdminNameError"></div>
            <br class="clear" />
    
                <?php echo $form['description']->renderLabel(__('Description')); ?>
                <?php echo $form['description']->render(array("class" => "formInput", "maxlength" => 255)); ?>
                <div class="errorHolder"></div>
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
<div class="paddingLeftRequired"><?php echo __('Fields marked with an asterisk') ?> <span class="required">*</span> <?php echo __('are required.') ?></div>
<div id="customerDialog" title="<?php echo __('Add Customer')?>"  style="display:none;">

<div class="dialogButtons">
    <form name="frmAddCustomer" id="frmAddCustomer" method="post" action="<?php echo url_for('admin/addCustomer'); ?>" >

	    <div class="newColumn">
                <?php echo $customerForm['customerName']->renderLabel(__('Name'). ' <span class="required">*</span>'); ?>
                <?php echo $customerForm['customerName']->render(array("class" => "formInput", "maxlength" => 52)); ?>
                <div id="errorHolderName"></div>
            </div>
	    <br class="clear"/>
	    
	    <div class="newColumn">
                <?php echo $customerForm['description']->renderLabel(__('Description')); ?>
                <?php echo $customerForm['description']->render(array("class" => "formInput", "maxlength" => 255)); ?>
		<div id="errorHolderDesc"></div>
            </div>
	    <br class="clear"/>
	    </form>
    <br class="clear"/>
    <input type="button" id="dialogSave" class="savebutton" value="<?php echo __('Save');?>" />
    <input type="button" id="dialogCancel" class="cancelbutton" value="<?php echo __('Cancel');?>" />
    <br class="clear"/>
    <div class="DigPaddingLeftRequired"><?php echo __('Fields marked with an asterisk') ?> <span class="required">*</span> <?php echo __('are required.') ?></div>
</div>
</div>

<script type="text/javascript">
    var employees = <?php echo str_replace('&#039;', "'", $form->getEmployeeListAsJson()) ?> ;
    var employeeList = eval(employees);
    var customers = <?php echo str_replace('&#039;', "'", $form->getCustomerListAsJson()) ?> ;
    var customerList = eval(customers);
    var numberOfProjectAdmins = <?php echo $form->numberOfProjectAdmins; ?>;
    var lang_typeHint = '<?php echo __("Type for hints")."..."; ?>';
    var lang_nameRequired = '<?php echo __("Customer name is required"); ?>';
    var lang_validCustomer = '<?php echo __("Enter a valid customer name"); ?>';
    var lang_projectRequired = '<?php echo __("Project name is required"); ?>';
    var lang_exceed50Chars = '<?php echo __("Cannot exceed 50 charactors"); ?>';
    var lang_exceed255Chars = '<?php echo __("Cannot exceed 255 charactors"); ?>';
    var custUrl = '<?php echo url_for("admin/saveCustomerJson"); ?>';
    var projectUrl = '<?php echo url_for("admin/addProject"); ?>';
    var lang_enterAValidEmployeeName = "<?php echo __("Enter a valid employee name"); ?>";
    var lang_identical_rows = "<?php echo __("Cannot assign same employee twice"); ?>";
</script>