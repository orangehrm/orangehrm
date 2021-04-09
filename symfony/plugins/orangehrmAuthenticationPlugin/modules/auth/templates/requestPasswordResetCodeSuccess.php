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
<?php use_javascript(plugin_web_path('orangehrmSecurityAuthenticationPlugin', 'js/passwordReset'));?>
<div  class="box">
    <?php include_partial('securityAuthenticationHeader'); ?>
    </br>
    <div class="head"><h1><?php echo __('Forgot Your Password?'); ?></h1>
    </div>
    <div class="inner">
        <?php include_partial('global/flash_messages'); ?>
        <div>
            <?php echo __("Please enter your username to identify your account to reset your password."); ?>
        </div>
        <form id="resetPasswordForm" action="<?php echo url_for('auth/requestPasswordResetCode'); ?>" method="post">

            <?php echo $form['_csrf_token']; ?>
            </br>
            <fieldset>
                <ol>
                    <li>
                        <?php echo $form['userName']->renderLabel(__('OrangeHRM Username')); ?>
                        <?php echo $form['userName']->render(); ?>
                        <?php echo $form['userName']->renderError() ?>
                    </li>
                </ol>
                <p>
                <div class="formbuttons">
                    <input type="submit" class="searchValues" name="button" id="btnSearchValues"

                           value="<?php echo __('Reset Password'); ?>"/>
                    <input type="button" class="cancel" name="button" id="btnCancel"

                           value="<?php echo __('Cancel'); ?>"/>
                </div>
                </p>
            </fieldset>
        </form>
    </div>
</div>

<?php include_partial('global/footer_copyright_social_links'); ?>
