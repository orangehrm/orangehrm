<?php

use_stylesheet('../../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css');
use_javascript('../../../scripts/jquery/ui/ui.core.js');
use_javascript('../../../scripts/jquery/ui/ui.draggable.js');
use_javascript('../../../scripts/jquery/ui/ui.resizable.js');
use_javascript('../../../scripts/jquery/ui/ui.dialog.js');

use_javascript('../orangehrmCoreLeavePlugin/js/defineLeaveTypeSuccess');
use_stylesheet('../orangehrmCoreLeavePlugin/css/defineLeaveTypeSuccess');
?>

<div class="formpage">

    <?php echo $form->getMessage(); ?>

    <div class="outerbox" style="width:auto;">

        <div class="mainHeading">
            <h2 class="paddingLeft"><?php echo $form->isUpdateMode() ? __('Edit') : __('Add'); ?>
                <?php echo __(' Leave Type'); ?></h2>
        </div>

        <form name="frmLeaveType" id="frmLeaveType" 
              action="<?php echo url_for('leave/defineLeaveType');?>" method="post">
        
        <?php echo $form['hdnLeaveTypeId']->render(); ?>
        <?php echo $form['hdnOriginalLeaveTypeName']->render(); ?>

        <table class="outerMost">
            <tr valign="top">
                <td width="70">
                <?php echo __('Name');?> <span class="required">*</span>
                </td>
                <td>
                    <?php echo $form['txtLeaveTypeName']->render(); ?>
                    <div>
                        <?php echo $form['txtLeaveTypeName']->renderError(); ?>
                    </div>

                    <?php echo $form['_csrf_token']; ?>
                </td>
            </tr>
        </table>

        <?php include_component('core', 'ohrmPluginPannel', array('location' => 'define-leave-type-extra-fields')); ?>
            
        <div class="formbuttons paddingLeft">
            <input type="button" id="saveButton" value="<?php echo __('Save'); ?>" class="savebutton" />
            <input type="reset"  id="resetButton" value="<?php echo __('Reset'); ?>" class="savebutton" />
            <input type="button" id="backButton" value="<?php echo __('Back'); ?>" class="savebutton" />
        </div>

    </form>

    </div>
</div>

<div class="paddingLeftRequired"><?php echo __('Fields marked with an asterisk')?> 
    <span class="required">*</span> <?php echo __('are required.')?>
</div>
    
<div id="undeleteDialog" title="OrangeHRM - <?php echo __('Confirmation Required')?>"  style="display:none;">
    <?php echo __('This is a deleted leave type. Reactivate it?'); ?><br /><br />

    <strong><?php echo __('Yes');?></strong> - <?php echo __('Leave type will be undeleted'); ?><br />
    <strong><?php echo __('No');?></strong> - 
    <?php 
        echo $form->isUpdateMode() ? __('This leave type will be renamed to the same name as the deleted leave type') :
                                     __('A new leave type will be created with same name');
    ?>
    <br />
    <strong><?php echo __('Cancel');?></strong> - <?php echo __('Will take no action'); ?><br /><br />
    <div class="dialogButtons">
        <input type="button" id="undeleteYes" class="savebutton" value="<?php echo __('Yes');?>" />
        <input type="button" id="undeleteNo" class="savebutton" value="<?php echo __('No');?>" />
        <input type="button" id="undeleteCancel" class="savebutton" value="<?php echo __('Cancel');?>" />
    </div>
</div> <!-- undeleteDialog -->

<form name="frmUndeleteLeaveType" id="frmUndeleteLeaveType" 
      action="<?php echo url_for('leave/undeleteLeaveType');?>" method="post">
    <?php echo $undeleteForm;?>
</form>

<script type="text/javascript">
//<![CDATA[
    var activeLeaveTypes = <?php echo $form->getActiveLeaveTypesJsonArray(); ?>;
    var deletedLeaveTypes = <?php echo $form->getDeletedLeaveTypesJsonArray(); ?>;
    
    var lang_LeaveTypeNameRequired = '<?php echo __('Please provide a leave type name'); ?>';
    var lang_LeaveTypeExists = '<?php echo __('This leave type exists'); ?>';
    var lang_LeaveTypeNameTooLong = '<?php echo __("Leave type name should be less than 30 characters"); ?>'; 
    
    var backButtonUrl = '<?php echo url_for('leave/leaveTypeList'); ?>';

//]]>
</script>
