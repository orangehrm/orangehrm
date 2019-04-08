
<?php use_javascript(plugin_web_path('orangehrmSecurityAuthenticationPlugin', 'js/securityAuthenticationCommon.js')); ?>
<?php use_stylesheet(plugin_web_path('orangehrmSecurityAuthenticationPlugin', 'css/securityAuthenticationConfigureSucess')); ?>

<div class="box">
    <div class="box">
        <div class="head">
            <h1><?php echo __('Reset Your Password?'); ?></h1>
        </div>

        <div class="inner">
            <?php include_partial('global/flash_messages'); ?>
            <form id="resetPasswordForm" action="" method="post">
                <fieldset>
                    <ol>
                        <input type="hidden" name="hdnMatchedByFied"
                               value="<?php echo $matchedByField; ?>" />
                        <input type="hidden" name="hdnMatchedValue"
                               value="<?php echo $matchedValue; ?>" />
                        <input type="hidden" name="hdnUsername"
                               value="<?php echo $username; ?>" />
                        <li><?php echo __('If you are sure that this is your account, please click Reset Password. We will send you an email to reset password.'); ?>
                        </li>
                        <li><?php echo __('Username or email should be listed:'); ?></li>
                        <li>
                            <?php echo __($matchedByField) . ':' . ' ' . $matchedValue; ?>
                        </li>
                    </ol>
                    <p>
                        <input type="submit" class="" id="btnSavePassword"
                               value="<?php echo __('Reset Password'); ?>" /> <input
                               type="button" class="reset" id="btnCancel"
                               value="<?php echo __('Cancel'); ?>" />
                    </p>

                </fieldset>
            </form>
        </div>
    </div>

    <?php include_partial('global/footer_copyright_social_links'); ?>
