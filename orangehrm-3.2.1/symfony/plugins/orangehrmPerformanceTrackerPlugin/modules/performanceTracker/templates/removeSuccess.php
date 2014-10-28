<div id="messagebar" class="<?php echo isset($messageType) ? "messageBalloon_{$messageType}" : ''; ?>" style="margin-left: 16px;width: 700px;">
    <span style="font-weight: bold;"><?php echo isset($message) ? $message : ''; ?></span>
</div>
<div class="outerbox" style="">
    <div class="mainHeading"><h2><?php echo __('Add Employee'); ?></h2></div>
    <div>
        <form id="frmAddEmp" method="post" action="<?php echo url_for('feedback/remove'); ?>"  style="width: auto">
            <table id="addEmployeeTbl"><?php //echo $form->render(); ?> </table>                       
            <div class="formbuttons">
                <input type="submit" class="savebutton" id="btnSave" value="<?php echo __("Save"); ?>"  />
                <input type="button" class="savebutton" id="btnCancel" value="<?php echo __("Cancel"); ?>" />
            </div>
        </form>
    </div>
</div>
<div class="paddingLeftRequired"><span class="required">*</span> <?php echo __(CommonMessages::REQUIRED_FIELD); ?></div>


