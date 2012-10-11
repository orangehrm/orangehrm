
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
	
    var user_UserNameRequired       = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var user_EmployeeNameRequired   = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var user_ValidEmployee          = '<?php echo __(ValidationMessages::INVALID); ?>';
    var user_UserPaswordRequired    = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var user_UserConfirmPassword    = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var user_samePassword           = "<?php echo __("Passwords do not match"); ?>";
    var user_Max20Chars             = '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 20)); ?>';
    var user_editLocation           = "<?php echo __("Edit User"); ?>";
    var userId                      = "<?php echo $userId ?>";
    var user_save                   = "<?php echo __("Save"); ?>";
    var user_edit                   = "<?php echo __("Edit"); ?>";
    var user_typeForHints           = "<?php echo __("Type for hints").'...';?>";
    var user_name_alrady_taken      = '<?php echo __(ValidationMessages::ALREADY_EXISTS); ?>';
    var isUniqueUserUrl             = '<?php echo url_for('admin/isUniqueUserJson'); ?>';
    var viewSystemUserUrl           = '<?php echo url_for('admin/viewSystemUsers'); ?>';
    var user_UserNameLength         =   '<?php echo __("Should have at least %number% characters", array('%number%' => 5)); ?>';
    var user_UserPasswordLength     =   '<?php echo __("Should have at least %number% characters", array('%number%' => 4)); ?>';
    var password_user               =   "<?php echo __("Very Weak").",".__("Weak").",".__("Better").",".__("Medium").",".__("Strong").",".__("Strongest")?>";
    var isEditMode                  = <?php echo ($form->edited)?'true':'false'; ?>;
    var ldapInstalled               = <?php echo ($sf_user->getAttribute('ldap.available'))?'true':'false'; ?>;
    var validator = null;

</script>
