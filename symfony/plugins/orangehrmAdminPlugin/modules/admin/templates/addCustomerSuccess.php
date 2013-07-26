<?php
use_javascript(plugin_web_path('orangehrmAdminPlugin', 'js/addCustomerSuccess')); 
?>
<?php if(($customerPermissions->canCreate() && empty($customerId)) || ($customerPermissions->canUpdate() && $customerId > 0)){?>
<div class="box"  id="addCustomer">
    <div class="head">
        <h1 id="addCustomerHeading"><?php echo __("Add Customer"); ?></h1>
    </div>
           
    <div class="inner">
            
        <?php include_partial('global/flash_messages'); ?>
        <form name="frmAddCustomer" id="frmAddCustomer" method="post" action="<?php echo url_for('admin/addCustomer'); ?>" >
            
            <?php echo $form['_csrf_token']; ?>
            <?php echo $form->renderHiddenFields(); ?>
            
            <fieldset>
                    
                <ol>
                    <li>
                        <?php echo $form['customerName']->renderLabel(__('Name'). ' <em>*</em>'); ?>
                        <?php echo $form['customerName']->render(array("class" => "block default editable valid", "maxlength" => 52)); ?>
                    </li>
                    
                    <li class="largeTextBox">
                        <?php echo $form['description']->renderLabel(__('Description')); ?>
                        <?php echo $form['description']->render(array("class" => "editable", "maxlength" => 255)); ?>
                    </li>
                    
                    <li class="required">
                        <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                    </li>
                </ol>
                    
                <p>
                    <input type="button" class="" name="btnSave" data-toggle="modal" id="btnSave" value="<?php echo __("Save"); ?>"/>
                    <input type="button" class="btn reset" name="btnCancel" id="btnCancel" value="<?php echo __("Cancel"); ?>"/>
                </p>
            
            </fieldset>
            
        </form>
        
    </div> <!-- End-inner -->
    
</div> <!-- End-addCustomer -->

<form name="frmUndeleteCustomer" id="frmUndeleteCustomer" method="post" action="<?php echo url_for('admin/undeleteCustomer'); ?>">
    <?php echo $undeleteForm; ?>
</form>

<a id="undeleteDialogLink" data-toggle="modal" href="#undeleteDialog" ></a>
<div class="modal hide" id="undeleteDialog">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3><?php echo __('OrangeHRM - Confirmation Required'); ?></h3>
    </div>
    <div class="modal-body">
        <p><?php echo __('This is a deleted customer. Reactivate again?'); ?></p>
        <p></p>
        <p><?php echo __('Yes'); ?> - <?php echo __('Customer will be undeleted'); ?></p>
        <p>
            <?php echo __('No'); ?> - 
            <?php
            echo $form->isUpdateMode() ? __('This customer will be renamed to the same name as the deleted customer') :
                    __('A new customer will be created with same name');
            ?>
        </p>
        <p><?php echo __('Cancel'); ?> - <?php echo __('Will take no action'); ?></p>
    </div>
    <div class="modal-footer">
        <input type="button" id="undeleteYes" class="btn" data-dismiss="modal" value="<?php echo __('Yes'); ?>" />
        <input type="button" id="undeleteNo" class="btn" data-dismiss="modal" value="<?php echo __('No'); ?>" />
        <input type="button" id="undeleteCancel" class="btn reset" data-dismiss="modal" value="<?php echo __('Cancel'); ?>" />
    </div>
</div> <!-- undeleteDialog -->
<?php }?>

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
    var deletedCustomers = <?php echo str_replace('&#039;', "'", $form->getDeletedCustomerListAsJson()) ?> ;
</script>
