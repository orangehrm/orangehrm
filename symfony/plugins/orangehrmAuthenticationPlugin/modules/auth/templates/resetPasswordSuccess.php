<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */
?>
<?php use_stylesheet(plugin_web_path('orangehrmSecurityAuthenticationPlugin', 'css/passwordStrength.css'));?>
<?php use_javascript(plugin_web_path('orangehrmSecurityAuthenticationPlugin', 'js/changePasswordStrength.js')); ?>

<?php if ($showForm): ?>
    <div class="box">
        <?php include_partial('securityAuthenticationHeader'); ?>
        </br>
        <div id="validationMsg"><?php echo isset($messageData) ? templateMessage($messageData) : '';?></div>
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
                            <?php echo $form['newPrimaryPassword']->render(); ?>
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
                        <input type="submit" class="savebutton" id="btnSavePassword"

                               value="<?php echo __('Save'); ?>" />

                        <input type="button" class="cancel" id="btnCancel"
                               value="<?php echo __('Cancel'); ?>" />
                    </p>
                </fieldset>
            </form>
        </div>
    </div>

<?php endif ?>
</div>

<script type="text/javascript">
    var lang_newPasswordRequired       = '<?php echo __js(ValidationMessages::REQUIRED); ?>';
    var lang_confirmNewPasswordRequired       = '<?php echo __js(ValidationMessages::REQUIRED); ?>';
    var lang_passwordMissMatch           = "<?php echo __js("Passwords do not match"); ?>";
    var lang_maxLengthExceeds             = '<?php echo __js(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 64)); ?>';
    var lang_UserPasswordLength     =   '<?php echo __js("Should have at least %number% characters", array('%number%' => 8)); ?>';
    var password_user               =   "<?php echo __js("Very Weak").",".__js("Weak").",".__js("Better").",".__js("Medium").",".__js("Strong").",".__js("Strongest")?>";
    var requiredStrengthCheckUrl = '<?php echo url_for('securityAuthentication/checkMinimumRequiredPasswordStrengthAjax') ?>';
    var lang_passwordStrengthInvalid = '<?php echo __js("Your password must contain a lower-case letter, an upper-case letter, a digit and a special character. Try a different password.");?>';
</script>

<?php include_partial('global/footer_copyright_social_links'); ?>

