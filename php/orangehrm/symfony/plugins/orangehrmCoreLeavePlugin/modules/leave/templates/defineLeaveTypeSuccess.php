<?php

use_stylesheet('../../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css');
use_javascript('../../../scripts/jquery/ui/ui.core.js');
use_javascript('../../../scripts/jquery/ui/ui.draggable.js');
use_javascript('../../../scripts/jquery/ui/ui.resizable.js');
use_javascript('../../../scripts/jquery/ui/ui.dialog.js');

use_javascripts_for_form($form);
use_stylesheets_for_form($form);

?>

<div class="formpage">

    <?php echo isset($templateMessage) ? templateMessage($templateMessage) : ''; ?>

    <div class="outerbox" style="width:auto;">

        <div class="mainHeading">
            <h2 class="paddingLeft"><?php echo $form->isUpdateMode() ? __('Edit Leave Type') : __('Add Leave Type'); ?></h2>
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
                    <?php if ($form['txtLeaveTypeName']->hasError()) { ?>
                    <div>
                        <?php echo $form['txtLeaveTypeName']->renderError(); ?>
                    </div>
                    <?php } ?>
                    <?php echo $form['_csrf_token']; ?>
                </td>
            </tr>
        </table>

        <?php include_component('core', 'ohrmPluginPannel', array('location' => 'define-leave-type-extra-fields')); ?>
            
        <div class="formbuttons paddingLeft">
<?php 
    $actionButtons = $form->getActionButtons();
    
    foreach($actionButtons as $button) {
        echo $button->render($id), "\n";        
    }

?>
        </div>

    </form>

    </div>
</div>

<div class="paddingLeftRequired"><span class="required">*</span> <?php echo __(CommonMessages::REQUIRED_FIELD); ?></div>
    
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

    var activeLeaveTypes = [];
    var deletedLeaveTypes = [];

    var lang_LeaveTypeNameRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_LeaveTypeExists = '<?php echo __(ValidationMessages::ALREADY_EXISTS); ?>';
    var lang_LeaveTypeNameTooLong = '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 50)); ?>'; 
    
    var backButtonUrl = '<?php echo url_for('leave/leaveTypeList'); ?>';

//]]>
</script>
