
<?php use_javascript(plugin_web_path('orangehrmAdminPlugin', 'js/changeUserPasswordSuccess')); ?>
<?php use_javascript(plugin_web_path('orangehrmAdminPlugin', 'js/password_strength')); ?>
   
<div id="systemUser"class="box">
        <div class="head"><h1 id="UserHeading"><?php echo __("Change Password"); ?></h1></div>
        <div class="inner">
            <?php include_partial('global/flash_messages'); ?>
        <form name="frmChangePassword" id="frmChangePassword" method="post" action="" >
            <?php echo $form['_csrf_token']; ?>
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