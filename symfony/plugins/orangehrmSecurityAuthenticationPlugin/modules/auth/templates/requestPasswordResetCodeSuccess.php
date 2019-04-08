<?php
/**
 * OrangeHRM Enterprise is a closed sourced comprehensive Human Resource Management (HRM)
 * System that captures all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM Inc is the owner of the patent, copyright, trade secrets, trademarks and any
 * other intellectual property rights which subsist in the Licensed Materials. OrangeHRM Inc
 * is the owner of the media / downloaded OrangeHRM Enterprise software files on which the
 * Licensed Materials are received. Title to the Licensed Materials and media shall remain
 * vested in OrangeHRM Inc. For the avoidance of doubt title and all intellectual property
 * rights to any design, new software, new protocol, new interface, enhancement, update,
 * derivative works, revised screen text or any other items that OrangeHRM Inc creates for
 * Customer shall remain vested in OrangeHRM Inc. Any rights not expressly granted herein are
 * reserved to OrangeHRM Inc.
 *
 * Please refer http://www.orangehrm.com/Files/OrangeHRM_Commercial_License.pdf for the license which includes terms and conditions on using this software.
 *
 */
?>
<?php use_stylesheet(plugin_web_path('orangehrmSecurityAuthenticationPlugin',
    'css/securityAuthenticationCommon.css')); ?>
<?php use_javascript(plugin_web_path('orangehrmSecurityAuthenticationPlugin',
    'js/securityAuthenticationCommon.js')); ?>

<div id="divContent" xmlns="http://www.w3.org/1999/html">

    <div class="box">
        <?php include_partial('securityAuthenticationHeader'); ?>
        <div class="head"><h1><?php echo __('Identify Your Account'); ?></h1>
        </div>
        <div class="inner">
            <?php include_partial('global/flash_messages'); ?>
            <div>
                <?php echo __("Before we can reset your password, you need to enter the information below to help identify your account"); ?>
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

                               value="<?php echo __('Search'); ?>"/>
                        <input type="button" class="cancel" name="button" id="btnCancel"

                               value="<?php echo __('Cancel'); ?>"/>
                    </div>
                    </p>
                </fieldset>
            </form>
        </div>
    </div>

    <?php include_partial('global/footer_copyright_social_links'); ?>
