
<?php
use_javascript(plugin_web_path('orangehrmAdminPlugin', 'js/changeUserPasswordSuccess'));
use_stylesheet(plugin_web_path('orangehrmSecurityAuthenticationPlugin', 'css/passwordStrength.css'));
?>

<div id="systemUser"class="box">
        <div class="head"><h1 id="UserHeading"><?php echo __("Change Password"); ?></h1></div>
        <div class="inner">
            <?php include_partial('global/flash_messages'); ?>
        <form name="frmChangePassword" id="frmChangePassword" method="post" action="" >
            <fieldset>
                
                <ol>
                    
                    <li>
            
            <label><?php echo __('Username'. ' <em>*</em>'); ?></label>
            <label class="valueHolder"><?php echo $username; ?></label>
            <?php echo $form->render(); ?>
            </li>
            <li class="required">
                        <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
            </li>
     
                </ol>
                
                <p>
                <input type="button" name="btnSave" id="btnSave" value="<?php echo __("Save"); ?>" />
                <input type="button" class="reset" name="btnCancel" id="btnCancel" value="<?php echo __("Cancel"); ?>" />
            </p>
            </fieldset>
                </form>
        </div>

        
    </div>

<script type="text/javascript">
	
    var lang_currentPasswordRequired       = '<?php echo __js(ValidationMessages::REQUIRED); ?>';
    var lang_newPasswordRequired       = '<?php echo __js(ValidationMessages::REQUIRED); ?>';
    var lang_confirmNewPasswordRequired       = '<?php echo __js(ValidationMessages::REQUIRED); ?>';
    var lang_passwordMissMatch           = "<?php echo __js("Passwords do not match"); ?>";
    var lang_maxLengthExceeds             = '<?php echo __js(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 64)); ?>';
    var lang_save                   = "<?php echo __js("Save"); ?>";
    var lang_edit                   = "<?php echo __js("Edit"); ?>";
    var lang_UserPasswordLength     =   '<?php echo __js("Should have at least %number% characters", array('%number%' => 8)); ?>';
    var password_user               =   "<?php echo __js("Very Weak").",".__js("Weak").",".__js("Better").",".__js("Medium").",".__js("Strong").",".__js("Strongest")?>";
    var requiredStrengthCheckUrl = '<?php echo url_for('securityAuthentication/checkMinimumRequiredPasswordStrengthAjax') ?>';
    var lang_passwordStrengthInvalid = '<?php echo __js("Your password must contain a lower-case letter, an upper-case letter, a digit and a special character. Try a different password.");?>';
</script>
