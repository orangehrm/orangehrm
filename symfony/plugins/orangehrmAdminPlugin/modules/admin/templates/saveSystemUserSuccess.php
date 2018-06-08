<?php
use_javascript(plugin_web_path('orangehrmAdminPlugin', 'js/systemUserSuccess'));
use_stylesheet(plugin_web_path('orangehrmSecurityAuthenticationPlugin', 'css/passwordStrength.css'));
?>

<div id="systemUser" class="box">
    
    <div class="head">
        <h1 id="UserHeading"><?php echo __("Add User"); ?></h1>
    </div>
        
    <div class="inner">
        
        <?php include_partial('global/flash_messages'); ?>

        <?php if ($employeeCount > 0) : ?>
            <form name="frmSystemUser" id="frmSystemUser" method="post" action="" >

                <fieldset>

                    <ol>
                        <?php echo $form->render(); ?>

                        <li class="required">
                            <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                        </li>
                    </ol>

                    <p>
                        <input type="button" class="addbutton" name="btnSave" id="btnSave" value="<?php echo __("Save"); ?>"/>
                        <input type="button" class="reset" name="btnCancel" id="btnCancel"value="<?php echo __("Cancel"); ?>"/>
                    </p>

                </fieldset>

            </form>
        <?php endif ?>

    </div>
    
</div>

<script type="text/javascript">
	
    var user_UserNameRequired       = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var user_EmployeeNameRequired   = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var user_ValidEmployee          = '<?php echo __(ValidationMessages::EMPLOYEE_DOES_NOT_EXIST); ?>';
    var user_UserPaswordRequired    = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var user_UserConfirmPassword    = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var user_samePassword           = "<?php echo __("Passwords do not match"); ?>";
    var user_Max20Chars             = '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 64)); ?>';
    var user_editLocation           = "<?php echo __("Edit User"); ?>";
    var userId                      = "<?php echo $userId ?>";
    var user_save                   = "<?php echo __("Save"); ?>";
    var user_edit                   = "<?php echo __("Edit"); ?>";
    var user_typeForHints           = "<?php echo __("Type for hints").'...';?>";
    var user_name_alrady_taken      = '<?php echo __(ValidationMessages::ALREADY_EXISTS); ?>';
    var isUniqueUserUrl             = '<?php echo url_for('admin/isUniqueUserJson'); ?>';
    var viewSystemUserUrl           = '<?php echo url_for('admin/viewSystemUsers'); ?>';
    var user_UserNameLength         = '<?php echo __("Should have at least %number% characters", array('%number%' => 5)); ?>';
    var user_UserPasswordLength     = '<?php echo __("Should have at least %number% characters", array('%number%' => 8)); ?>';
    var password_user               = "<?php echo __("Very Weak").",".__("Weak").",".__("Better").",".__("Medium").",".
            __("Strong").",".__("Strongest")?>";
    var isEditMode                  = '<?php echo ($form->edited)?'true':'false'; ?>';
    var ldapInstalled               = '<?php echo ($sf_user->getAttribute('ldap.available'))?'true':'false'; ?>';
    var validator = null;
    var openIdEnabled = "<?php echo $openIdEnabled; ?>";
    var lang_maxLengthExceeds = '<?php echo __("Password length should be less than %max% characters. Try a different password.", array('%max%' => 64)); ?>';
    var lang_passwordStrengthInvalid = '<?php echo __("Your password must contain a lower-case letter, an upper-case letter, a digit and a special character. Try a different password.");?>';
    var requiredStrengthCheckUrl = '<?php echo url_for('securityAuthentication/checkMinimumRequiredPasswordStrengthAjax') ?>';


</script>
