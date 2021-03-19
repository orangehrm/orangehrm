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
 * Boston, MA 02110-1301, USA
 */

use_stylesheet(plugin_web_path('orangehrmHelpPlugin', 'css/indexSuccess'));
?>

<div class="box">
    <div class="head">
        <h1><?php echo __('Support'); ?></h1>
    </div>
    <div class="inner">
        <div class="inner help-inner-row">
            <div class="help-header">
                <div class="help-column help-header-icon">
                    <i class="fas fa-window-maximize help-header-icon"></i>
                </div>
                <h2><?php echo __('Getting Started with OrangeHRM'); ?></h2>
            </div>
            <div class="box">
                <p><?php echo __(
                        'Learning how to use a new application can be challenging. At OrangeHRM, we are committed to providing you with the necessary knowledge and skills required to fully utilize the application thereby allowing you to quickly and efficiently manage your HR processes.'
                    ); ?></p>
                <br>
                <p><?php echo __(
                        'The following information repositories are available to help you understand the application:'
                    ); ?></p>
                <br>
                <div class="main-info-container main-help">
                    <div class="info-container">
                        <div>
                            <img src="<?php echo theme_path('images/help.png')?>" alt="Help-Portal" width="40px">
                        </div>
                        <div class="main-inner-container">
                            <p class="info-header"><?php echo __(
                                    'Help Portal'
                                ); ?></p>

                            <p><?php echo __(
                                    'OrangeHRM Open Source Help portal contains helpful articles and tutorial videos allowing you to quickly master the application. Should you wish to know how to apply for leave or how to submit a timesheet? Simply search in our help portal and the relevant articles relating to your search will appear.'
                                ); ?></p>
                        </div>
                    </div>
                    <div class="help-button-container">
                        <a href="https://opensourcehelp.orangehrm.com" target="_blank">
                            <input class="action-button" type="button" name="button"  value="<?php echo __('Help Portal'); ?>" />
                        </a>
                    </div>
                </div>
                <div class="main-info-container main-block">
                    <div class="info-container">
                        <div>
                            <img src="<?php echo theme_path('images/free-demo.png')?>" alt="Help-Portal" width="40px">
                        </div>
                        <div class="main-inner-container" >
                            <p class="info-header"><?php echo __(
                                    'Free Demo'
                                ); ?></p>

                            <p><?php echo __(
                                    'If you require direct help in navigating the system, you can schedule a demo free of charge and our team will personally get in touch with you to help  you understand how the application works.'
                                ); ?></p>
                        </div>
                    </div>
                    <div class="button-container">
                        <a href="https://www.orangehrm.com/open-source/demo" target="_blank">
                            <input class="action-button" type="button" name="button"  value="<?php echo __('Free Demo'); ?>" />
                        </a>
                    </div>
                </div>
                <div class="main-info-container main-block">
                    <div class="info-container">
                        <div>
                            <img src="<?php echo theme_path('images/cloud.png')?>" alt="Help-Portal" width="40px">
                        </div>
                        <div class="main-inner-container">
                            <p class="info-header"><?php echo __(
                                    'Free Cloud Hosting'
                                ); ?></p>

                            <p><?php echo __(
                                    'Additionally, OrangeHRM can host your Opensource system on our Cloud servers. If you are interested, you may subscribe to free cloud hosting of your instance to ensure that your employee information stays secure and is readily available at all times.'
                                ); ?></p>
                        </div>
                    </div>
                    <div class="button-container">
                        <a href="https://www.orangehrm.com/open-source/open-source-on-demand" target="_blank">
                            <input class="action-button" type="button" name="button"  value="<?php echo __('Free Cloud Hosting'); ?>" />
                        </a>
                    </div>
                </div>
                <div class="main-info-container main-block">
                    <div class="info-container">
                        <div>
                            <img src="<?php echo theme_path('images/customer-support.png')?>" alt="Help-Portal" width="40px">
                        </div>
                        <div class="main-inner-container">
                            <p class="info-header"><?php echo __(
                                    'Customer Support'
                                ); ?></p>
                            <div>
                            <p class="customer-support-inner-text"><?php echo __(
                                    'Should you experience any issues, please do not hesitate to contact us on'
                                ); ?></p>
                                <p class="text-gap customer-support-inner-text"><a href="mailto:ossuport@orangehrm.com" class="support-link"> ossuport@orangehrm.com</a></p>
                                <p class="text-gap customer-support-inner-text"><?php echo __(
                                        'We will be delighted to help'
                                    ); ?></p>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="main-info-container main-block">
                    <div class="info-container">
                        <div>
                            <img src="<?php echo theme_path('images/mobile.png')?>" alt="Help-Portal" width="40px">
                        </div>
                        <div class="main-inner-container">
                            <p class="info-header"><?php echo __(
                                    'Mobile Application'
                                ); ?></p>
                            <div>
                                <p><?php echo __(
                                        "We would also like to remind you to check out our mobile app which supports  Android and iOS and enables you to manage your HR needs quickly and securely via your mobile device."
                                    ); ?></p>
                            </div>
                            <div class="store-icon-container">
                                <div class="help-column">
                                    <a target="_blank"
                                       href='https://play.google.com/store/apps/details?id=com.orangehrm.opensource'>
                                        <img class="play-store-img" alt='Get it on Google Play'
                                             src='<?php echo theme_path('images/play_store_en_US.png') ?>'/>
                                    </a>
                                </div>
                                <div class="help-column">
                                    <a target="_blank" href='https://apps.apple.com/us/app/orangehrm/id1527247547'>
                                        <img class="app-store-img" alt='Download on the App Store'
                                             src='<?php echo theme_path('images/app_store_en_US.svg') ?>'/>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="button-container">
                        <p>
                            <img alt='Mobile app QR code'
                                 src='<?php echo theme_path('images/mobile_app_qr.png') ?>'/>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="inner help-inner-row">
            <div class="help-header">
                <div class="help-column help-header-icon">
                    <i class="fas fa-lg fa-mobile-alt help-header-icon"></i>
                </div>
                <h2><?php echo __('OrangeHRM Open Source Mobile App - How to Configure URL and Login'); ?></h2>
            </div>
            <div class="box">
                <p><?php echo __(
                        'Upon opening the application, you will be required to configure the OrangeHRM instance that you wish to access via your mobile device.'
                    ); ?></p>
                <br>
                <p><strong><?php echo __('Step 01:'); ?></strong> <?php echo __(
                        'Enter the OrangeHRM instance URL.'
                    ); ?>
                </p>
                <br>
                <?php
                if ($isHttps) {
                    echo "<p>" . __("Your Instance URL is:") . " <a class='instance-link'>" . $url . "</a></p>";
                } else {
                    echo "<p class='instance-warning'><span class='warning instance-warning'>" . __(
                            "OrangeHRM Opensource mobile app does not support your instance. Please contact your system administrator for more information."
                        ) . "</span></p>";
                }
                ?>
                <br>
                <p>
                    <img class="help-ss" alt='Select instance screen'
                         src='<?php echo theme_path('images/mobile_select_instance.png') ?>'/>
                </p>
                <br>
                <p><strong><?php echo __('Step 02:'); ?></strong> <?php echo __('Click Continue'); ?></p>
                <br>
                <p><?php echo __(
                        'Once the URL is entered successfully, it will redirect to a page where the login information is requested.'
                    ); ?></p>
                <br>
                <p><strong><?php echo __('Step 03:'); ?></strong> <?php echo __('LOGIN'); ?></p>
                <br>
                <p><?php echo __(
                        'Ensure the OrangeHRM username and password credentials are entered correctly, and click LOGIN. You will be granted access to the system after successful validation of your username and password credentials.'
                    ); ?></p>
                <br>
                <p>
                    <img class="help-ss" alt='Login screen'
                         src='<?php echo theme_path('images/mobile_login.png') ?>'/>
                </p>
                <br>
                <p><?php echo __(
                        'Directly below the login fields, the URL of the system you are connecting to will be displayed so that you can verify the URL specified is accurate. '
                    ); ?></p>
            </div>
        </div>
    </div>
</div>
