<?php use_stylesheet(plugin_web_path('orangehrmSecurityAuthenticationPlugin','css/securityAuthenticationCommon.css')); ?>
<?php use_javascript(plugin_web_path('orangehrmAdminPlugin', 'js/password_strength')); ?>
<?php use_stylesheet(plugin_web_path('orangehrmSecurityAuthenticationPlugin', 'css/passwordStrength.css'));?>
<?php use_javascript(plugin_web_path('orangehrmSecurityAuthenticationPlugin','js/securityAuthenticationCommon.js')); ?>
<?php use_javascript(plugin_web_path('orangehrmSecurityAuthenticationPlugin', 'js/changePasswordStrength.js')); ?>
<!--div id="divContent"-->
<?php include_partial('securityAuthenticationHeader'); ?>
    <div id="validationMsg"><?php echo isset($messageData) ? templateMessage($messageData) : '';?></div>

<?php if ($showForm): ?>
    <!--div class="outerbox"  style="width: 500px; margin: 10px auto;" -->
    <div class="box">
        <div class="head"><h1><?php echo __('Enter A New Password'); ?></h1>
        </div>
        <div class="inner">
            <?php include_partial('global/flash_messages'); ?>
            <form  id="resetPasswordForm" action=""  method="post">
                <fieldset>
                    <ol>
                        <li>
                            <?php echo $form['_csrf_token']; ?>
                        </li>

                            <li>
                                <?php echo $form['newPrimaryPassword']->renderLabel(__('New Password') . ' <em>*</em>'); ?>
                                <?php echo $form['newPrimaryPassword']->render(array("title" => __("Complexity of the password can be increase by using a mix of special characters, number, upper and lower case characters"))); ?>
                                <?php echo $form['newPrimaryPassword']->renderError() ?>
                                <label class="score"></label>
                            </li>

                            <li>
                                <?php echo $form['primaryPasswordConfirmation']->renderLabel(__('Confirm New Password') . ' <em>*</em>'); ?>
                                <?php echo $form['primaryPasswordConfirmation']->render(); ?>
                                <?php echo $form['primaryPasswordConfirmation']->renderError(); ?>
                            </li>                            

                            <li class="required">
                                <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                            </li>
                    </ol>
                    <p>
                        <!--div class="formbuttons"-->
                        <input type="submit" class="savebutton" id="btnSavePassword"

                               value="<?php echo __('Save'); ?>" />

                        <input type="button" class="cancel" id="btnCancel"
                               value="<?php echo __('Cancel'); ?>" />
                        <!--/div-->
                    </p>
                </fieldset> 
            </form>
        </div>
    </div>
    <!--/div-->
<?php ELSE: ?>
<?php include_partial('global/flash_messages'); ?>
<?php endif; ?>
</div>

<script type="text/javascript">
    var lang_newPasswordRequired       = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_confirmNewPasswordRequired       = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_passwordMissMatch           = "<?php echo __("Passwords do not match"); ?>";
    var lang_maxLengthExceeds             = '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 64)); ?>';
    var lang_UserPasswordLength     =   '<?php echo __("Should have at least %number% characters", array('%number%' => 8)); ?>';
    var password_user               =   "<?php echo __("Very Weak").",".__("Weak").",".__("Better").",".__("Medium").",".__("Strong").",".__("Strongest")?>";
    var requiredStrengthCheckUrl = '<?php echo url_for('securityAuthentication/checkMinimumRequiredPasswordStrengthAjax') ?>';
    var lang_passwordStrengthInvalid = '<?php echo __("Your password must contain a lower-case letter, an upper-case letter, a digit and a special character. Try a different password.");?>';
</script>
<?php include_partial('global/footer_copyright_social_links'); ?>

