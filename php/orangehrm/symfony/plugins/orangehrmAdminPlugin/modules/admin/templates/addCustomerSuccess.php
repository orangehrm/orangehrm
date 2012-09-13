<?php use_stylesheet('../orangehrmAdminPlugin/css/addCustomerSuccess'); ?>
<?php use_javascript('../orangehrmAdminPlugin/js/addCustomerSuccess'); ?>
<?php
use_stylesheet('../../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css');
use_javascript('../../../scripts/jquery/ui/ui.core.js');
use_javascript('../../../scripts/jquery/ui/ui.draggable.js');
use_javascript('../../../scripts/jquery/ui/ui.resizable.js');
use_javascript('../../../scripts/jquery/ui/ui.dialog.js');
?>

<?php echo isset($templateMessage) ? templateMessage($templateMessage) : ''; ?>
<div id="messagebar" class="<?php echo isset($messageType) ? "messageBalloon_{$messageType}" : ''; ?>" >
    <span><?php echo isset($message) ? $message : ''; ?></span>
</div>

<div id="addCustomer">
    <div class="outerbox">

        <div class="mainHeading"><h2 id="addCustomerHeading"><?php echo __("Add Customer"); ?></h2></div>
        <form name="frmAddCustomer" id="frmAddCustomer" method="post" action="<?php echo url_for('admin/addCustomer'); ?>" >
            <?php echo $form->renderHiddenFields(); ?>
            <br class="clear"/>
            <div class="newColumn">
                <?php echo $form['customerName']->renderLabel(__('Name') . ' <span class="required">*</span>'); ?>
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

<div id="undeleteDialog" title="<?php echo __('OrangeHRM - Confirmation Required'); ?>"  style="display:none;">
    <?php echo __('This is a deleted customer. Reactivate again?'); ?><br /><br />

    <strong><?php echo __('Yes'); ?></strong> - <?php echo __('Customer will be undeleted'); ?><br />
    <strong><?php echo __('No'); ?></strong> - 
    <?php
    echo $form->isUpdateMode() ? __('This customer will be renamed to the same name as the deleted customer') :
            __('A new customer will be created with same name');
    ?>
    <br />
    <strong><?php echo __('Cancel'); ?></strong> - <?php echo __('Will take no action'); ?><br /><br />
    <div class="dialogButtons">
        <input type="button" id="undeleteYes" class="savebutton" value="<?php echo __('Yes'); ?>" />
        <input type="button" id="undeleteNo" class="savebutton" value="<?php echo __('No'); ?>" />
        <input type="button" id="undeleteCancel" class="savebutton" value="<?php echo __('Cancel'); ?>" />
    </div>
</div> <!-- undeleteDialog -->

<form name="frmUndeleteCustomer" id="frmUndeleteCustomer" 
      action="<?php echo url_for('admin/undeleteCustomer'); ?>" method="post">
          <?php echo $undeleteForm; ?>
</form>

<script type="text/javascript">
    var customers = <?php echo str_replace('&#039;', "'", $form->getCustomerListAsJson()) ?> ;
    var customerList = eval(customers);
    var deletedCustomers = <?php echo str_replace('&#039;', "'", $form->getDeletedCustomerListAsJson()) ?> ;
    var lang_customerNameRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_exceed50Charactors = '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 50)); ?>';
    var lang_exceed255Charactors = '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 250)); ?>';
    var lang_editCustomer = "<?php echo __("Edit Customer"); ?>";
    var lang_uniqueName = '<?php echo __(ValidationMessages::ALREADY_EXISTS); ?>';
    var lang_edit = "<?php echo __("Edit"); ?>";
    var lang_save = "<?php echo __("Save"); ?>";
    var customerId = '<?php echo $customerId; ?>';
    var cancelBtnUrl = '<?php echo url_for('admin/viewCustomers'); ?>';
</script>