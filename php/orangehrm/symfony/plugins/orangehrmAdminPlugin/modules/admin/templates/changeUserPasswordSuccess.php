
<?php
use_stylesheet('../../../themes/orange/css/jquery/jquery.autocomplete.css');
use_stylesheet('../../../themes/orange/css/ui-lightness/jquery-ui-1.7.2.custom.css');

use_javascript('../../../scripts/jquery/ui/ui.core.js');
use_javascript('../../../scripts/jquery/ui/ui.dialog.js');
use_javascript('../../../scripts/jquery/jquery.autocomplete.js');
?>
<?php use_stylesheet('../orangehrmAdminPlugin/css/changeUserPasswordSuccess'); ?>
<?php use_javascript('../orangehrmAdminPlugin/js/changeUserPasswordSuccess'); ?>
<?php use_javascript('../orangehrmAdminPlugin/js/password_strength'); ?>

<div id="messagebar">
<?php echo isset($templateMessage) ? templateMessage($templateMessage) : ''; ?>
</div>
    
<div id="systemUser">
    <div class="outerbox">

        <div class="mainHeading"><h2 id="UserHeading"><?php echo __("Change Password"); ?></h2></div>
        <form name="frmChangePassword" id="frmChangePassword" method="post" action="" >

            <div id="usernameValue">
            <label><?php echo __('Username'); ?></label>
            <label class="valueHolder"><?php echo $username; ?></label>
            </div>
            <br class="clear"/>
            <?php echo $form->render(); ?>
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
<div class="paddingLeftRequired"><span class="required">*</span> <?php echo __(CommonMessages::REQUIRED_FIELD); ?></div>

<script type="text/javascript">
	
    var lang_currentPasswordRequired       = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_newPasswordRequired       = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_confirmNewPasswordRequired       = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_passwordMissMatch           = "<?php echo __("Passwords do not match"); ?>";
    var lang_maxLengthExceeds             = '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 20)); ?>';
    var lang_save                   = "<?php echo __("Save"); ?>";
    var lang_edit                   = "<?php echo __("Edit"); ?>";
    var lang_UserPasswordLength     =   '<?php echo __("Should have at least %number% characters", array('%number%' => 4)); ?>';
    var password_user               =   "<?php echo __("Very Weak").",".__("Weak").",".__("Better").",".__("Medium").",".__("Strong").",".__("Strongest")?>";
</script>