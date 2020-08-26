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
        <h1><?php echo __('Help & Support'); ?></h1>
    </div>
    <div class="inner">
        <div class="inner help-inner-row">
            <div class="help-header">
                <div class="help-column help-header-icon">
                    <i class="fas fa-lg fa-mobile-alt help-header-icon"></i>
                </div>
                <h2><?php echo __('OrangeHRM Open Source Mobile App - How to Download the Application'); ?></h2>
            </div>
            <div class="box">
                <p><?php echo __(
                        'OrangeHRM Open Source mobile application allows you to apply leave, assign leave and approve leave via a mobile device. This application is available for download on both iOS and Android platforms.'
                    ); ?></p>
                <br>
                <p><?php echo __(
                        'To install and get started.'
                    ); ?></p>
                <br>
                <p><?php echo __(
                        'Download and install the application from the App Store or Play Store.'
                    ); ?>
                </p>
                <br>
                <p><?php echo __(
                        'The following links direct you to find the app in the Play Store / App Store.'
                    ); ?></p>
                <p>
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
                </p>
                <p><?php echo __(
                        'You may alternatively scan for the following QR code from your mobile phone camera to find and download the application.'
                    ); ?></p>
                <br>
                <p>
                    <img alt='Mobile app QR code'
                         src='<?php echo theme_path('images/mobile_app_qr.png') ?>'/>
                </p>
                <br>
                <p><?php echo __(
                        'Once the installation is complete, open the application and follow the steps outlined below to access your OrangeHRM system.'
                    ); ?></p>
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
