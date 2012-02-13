
<?php
use_stylesheet('../../../themes/orange/css/jquery/jquery.autocomplete.css');
use_stylesheet('../../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css');

use_javascript('../../../scripts/jquery/ui/ui.core.js');
use_javascript('../../../scripts/jquery/ui/ui.dialog.js');
use_javascript('../../../scripts/jquery/jquery.autocomplete.js');
?>
<?php use_stylesheet('../orangehrmAdminPlugin/css/systemUserSuccess'); ?>
<?php use_javascript('../orangehrmAdminPlugin/js/systemUserSuccess'); ?>
<?php use_javascript('../orangehrmAdminPlugin/js/password_strength'); ?>

<?php echo isset($templateMessage) ? templateMessage($templateMessage) : ''; ?>
<div id="messagebar" class="<?php echo isset($messageType) ? "messageBalloon_{$messageType}" : ''; ?>" >
    <span><?php echo isset($message) ? $message : ''; ?></span>
</div>

<div id="systemUser">
    <div class="outerbox">

        <div class="mainHeading"><h2 id="UserHeading"><?php echo __("Add User"); ?></h2></div>
        <form name="frmSystemUser" id="frmSystemUser" method="post" action="" >

            <?php echo $form['_csrf_token']; ?>
            <?php echo $form->renderHiddenFields(); ?>
            <br class="clear"/>

            <?php echo $form['userType']->renderLabel(__('User Type') . ' <span class="required">*</span>'); ?>
            <?php echo $form['userType']->render(array("class" => "formSelect", "maxlength" => 3)); ?>
            <div class="errorHolder"></div>
            <br class="clear"/>

            <?php echo $form['employeeName']->renderLabel(__('Employee Name') . ' <span class="required">*</span>'); ?>
            <?php if (!$form->edited) {
                echo $form['employeeName']->render(array("class" => "formInputText inputFormatHint", "maxlength" => 200, "value" => __("Type for hints")."..."));
            } else {
                echo $form['employeeName']->render(array("class" => "formInputText", "maxlength" => 200));
            } ?>
            <div class="errorHolder"></div>
            <br class="clear"/>

            <?php echo $form['userName']->renderLabel(__('Username') . ' <span class="required">*</span>'); ?>
            <?php echo $form['userName']->render(array("class" => "formInputText", "maxlength" => 20)); ?>
            <div class="errorHolder"></div>
            <br class="clear"/>

            <?php if (!$form->edited) {
                echo $form['password']->renderLabel(__('Password') . ' <span class="required">*</span>');
            } else {
                echo $form['password']->renderLabel(__('Password'));
            } ?>
            <?php echo $form['password']->render(array("class" => "formInputText", "maxlength" => 20)); ?><div class="errorHolder"></div><?php echo $form['password']->renderLabel(' ', array('class' => 'score')); ?>

            <br class="clear"/>

<?php if (!$form->edited) {
    echo $form['confirmPassword']->renderLabel(__('Confirm Password') . ' <span class="required">*</span>');
} else {
    echo $form['confirmPassword']->renderLabel(__('Confirm Password'));
} ?>
<?php echo $form['confirmPassword']->render(array("class" => "formInputText", "maxlength" => 20)); ?>
            <div class="errorHolder"></div>
            <br class="clear"/>

<?php echo $form['status']->renderLabel(__('Status') . ' <span class="required">*</span>'); ?>
<?php echo $form['status']->render(array("class" => "formSelect", "maxlength" => 3)); ?>
            <div class="errorHolder"></div>
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
	
    var user_UserNameRequired       = "<?php echo __("Username is required"); ?>";
    var user_EmployeeNameRequired   = "<?php echo __("Employee name is required"); ?>";
    var user_ValidEmployee          = "<?php echo __("Select valid employee"); ?>";
    var user_UserPaswordRequired    = "<?php echo __("Password is required"); ?>";
    var user_UserConfirmPassword    = "<?php echo __("Confirm password is required"); ?>";
    var user_samePassword           = "<?php echo __("Passwords do not match"); ?>";
    var user_Max20Chars             = "<?php echo __("Cannot exceed 20 charactors"); ?>";
    var user_editLocation           = "<?php echo __("Edit User"); ?>";
    var userId                      = "<?php echo $userId ?>";
    var user_save                   = "<?php echo __("Save"); ?>";
    var user_edit                   = "<?php echo __("Edit"); ?>";
    var employees                   = <?php echo str_replace('&#039;', "'", $form->getEmployeeListAsJson()) ?> ;
    var employeesArray              = eval(employees);
    var user_typeForHints           = "<?php echo __("Type for hints").'...';?>";
    var user_name_alrady_taken      =   "<?php echo __("Username already taken"); ?>";
    var isUniqueUserUrl             = '<?php echo url_for('admin/isUniqueUserJson'); ?>';
    var viewSystemUserUrl             = '<?php echo url_for('admin/viewSystemUsers'); ?>';
    var user_UserNameLength         =   "<?php echo __("User Name should have at least 5 characters"); ?>";
    var user_UserPasswordLength     =   "<?php echo __("Password should have at least 4 characters"); ?>";
    var password_user               =   "<?php echo __("Very Weak").",".__("Weak").",".__("Better").",".__("Medium").",".__("Strong").",".__("Strongest")?>";

</script>
