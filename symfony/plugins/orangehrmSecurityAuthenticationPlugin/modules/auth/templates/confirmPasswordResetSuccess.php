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
<?php include_partial('securityAuthenticationHeader');?>
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
</div>
<?php include_partial('global/footer_copyright_social_links'); ?>
